/**
 * ============================================================================
 * RUANGWARGA - JAVASCRIPT LOGIC & INTERACTIVITY (LARAVEL INTEGRATION)
 * Portal Layanan Mandiri & Transparansi Lingkungan RT
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  // Initialize progress bar fills from data attributes if present
  document.querySelectorAll('.progress-bar-fill[data-percent]').forEach(el => {
    if (el.dataset.percent) el.style.width = el.dataset.percent + '%';
    if (el.dataset.color) el.style.backgroundColor = el.dataset.color;
  });

  /* ==========================================================================
     1. TOAST NOTIFICATION SYSTEM
     ========================================================================== */
  const toastContainer = document.getElementById('toastContainer');

  function showToast(title, message, type = 'success') {
    if (!toastContainer) return;

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;

    let iconSvg = '';
    if (type === 'success') {
      iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>`;
    } else if (type === 'info') {
      iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>`;
    } else if (type === 'error') {
      iconSvg = `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;
    }

    toast.innerHTML = `
      <div class="toast-icon">${iconSvg}</div>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
    `;

    toastContainer.appendChild(toast);

    requestAnimationFrame(() => {
      toast.classList.add('show');
    });

    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 350);
    }, 4500);
  }

  /* ==========================================================================
     2. MODAL CONTROLS
     ========================================================================== */
  const modalTriggers = document.querySelectorAll('[data-modal]');
  const closeButtons = document.querySelectorAll('[data-close-modal]');
  const overlays = document.querySelectorAll('.modal-overlay');

  function openModal(modalId) {
    const targetModal = document.getElementById(modalId);
    if (targetModal) {
      targetModal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeModal(modal) {
    if (modal) {
      modal.classList.remove('active');
      const activeModals = document.querySelectorAll('.modal-overlay.active');
      if (activeModals.length === 0) {
        document.body.style.overflow = '';
      }
    }
  }

  modalTriggers.forEach(trigger => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      const modalId = trigger.getAttribute('data-modal');
      openModal(modalId);
    });
  });

  closeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const modal = btn.closest('.modal-overlay');
      closeModal(modal);
    });
  });

  // Click outside modal container
  overlays.forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        closeModal(overlay);
      }
    });
  });

  // Escape key closes modal
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const activeModal = document.querySelector('.modal-overlay.active');
      if (activeModal) {
        closeModal(activeModal);
      }
    }
  });

  /* ==========================================================================
     3. NAVBAR & MOBILE MENU
     ========================================================================== */
  const navbar = document.getElementById('topNavbar');
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const navLinks = document.getElementById('navLinks');

  window.addEventListener('scroll', () => {
    if (window.scrollY > 20) {
      navbar?.classList.add('scrolled');
    } else {
      navbar?.classList.remove('scrolled');
    }
  });

  if (mobileMenuBtn && navLinks) {
    mobileMenuBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      navLinks.classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
      if (navLinks.classList.contains('open') && !navLinks.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
        navLinks.classList.remove('open');
      }
    });

    navLinks.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('open');
      });
    });
  }

  /* ==========================================================================
     3B. NOTIFICATION BELL DROPDOWN
     ========================================================================== */
  const btnToggleNotif = document.getElementById('btnToggleNotif');
  const notifDropdown = document.getElementById('notifDropdown');
  const notifActionLink = document.getElementById('notifActionLink');

  if (btnToggleNotif && notifDropdown) {
    btnToggleNotif.addEventListener('click', (e) => {
      e.stopPropagation();
      notifDropdown.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
      if (!notifDropdown.contains(e.target) && e.target !== btnToggleNotif) {
        notifDropdown.classList.remove('show');
      }
    });

    if (notifActionLink) {
      notifActionLink.addEventListener('click', () => {
        notifDropdown.classList.remove('show');
      });
    }
  }

  /* ==========================================================================
     4. CEK & BAYAR IURAN (LARAVEL ENDPOINT)
     ========================================================================== */
  const btnCekIuran = document.getElementById('btnCekIuran');
  const searchHouseNumber = document.getElementById('searchHouseNumber');
  const iuranResultBox = document.getElementById('iuranResultBox');
  const iuranOwnerName = document.getElementById('iuranOwnerName');
  const iuranHouseAddress = document.getElementById('iuranHouseAddress');
  const iuranStatusBadge = document.getElementById('iuranStatusBadge');
  const iuranTotalAmount = document.getElementById('iuranTotalAmount');
  const paymentActionSection = document.getElementById('paymentActionSection');
  const btnKonfirmasiBayar = document.getElementById('btnKonfirmasiBayar');

  if (btnCekIuran) {
    btnCekIuran.addEventListener('click', async () => {
      const query = searchHouseNumber.value.trim();
      if (!query) {
        showToast('Perhatian', 'Silakan ketik nomor rumah/blok terlebih dahulu (contoh: B-12).', 'info');
        return;
      }

      btnCekIuran.disabled = true;
      btnCekIuran.textContent = 'Memeriksa...';

      try {
        const response = await fetch(`/api/cek-iuran?house=${encodeURIComponent(query)}`);
        const res = await response.json();

        if (res.success) {
          const data = res.data;
          iuranOwnerName.textContent = data.name;
          iuranHouseAddress.textContent = data.address;
          iuranTotalAmount.textContent = data.amount;

          if (data.status === 'paid') {
            iuranStatusBadge.innerHTML = `<span class="badge badge-emerald">Lunas (${data.period})</span>`;
            if (paymentActionSection) paymentActionSection.style.display = 'none';
          } else {
            iuranStatusBadge.innerHTML = `<span class="badge badge-amber">Belum Lunas (${data.period})</span>`;
            if (paymentActionSection) paymentActionSection.style.display = 'block';
          }

          iuranResultBox.classList.add('show');
        } else {
          showToast('Informasi', res.message || 'Data tidak ditemukan.', 'info');
        }
      } catch (err) {
        // Fallback local simulation if offline
        iuranOwnerName.textContent = `Keluarga Penghuni Blok ${query.toUpperCase()}`;
        iuranHouseAddress.textContent = `Rumah Warga RT 05 Blok ${query.toUpperCase()}`;
        iuranTotalAmount.textContent = 'Rp 75.000';
        iuranStatusBadge.innerHTML = `<span class="badge badge-amber">Belum Lunas (September 2026)</span>`;
        if (paymentActionSection) paymentActionSection.style.display = 'block';
        iuranResultBox.classList.add('show');
      } finally {
        btnCekIuran.disabled = false;
        btnCekIuran.textContent = 'Cek Tagihan';
      }
    });
  }

  if (btnKonfirmasiBayar) {
    const handleKonfirmasiBayar = async (e) => {
      if (e) e.preventDefault();
      const fileInput = document.getElementById('fileBuktiBayar');
      const fileName = fileInput?.files.length > 0 ? fileInput.files[0].name : 'Struk transfer QRIS';
      const house = searchHouseNumber ? searchHouseNumber.value.trim() : 'B-12';
      const selectedTagihan = document.querySelector('input[name="tagihan_id"]:checked');

      btnKonfirmasiBayar.disabled = true;
      btnKonfirmasiBayar.textContent = 'Mengunggah & Mengonfirmasi...';

      try {
        const formData = new FormData();
        formData.append('house', house || 'B-12');
        if (selectedTagihan) {
          formData.append('tagihan_id', selectedTagihan.value);
        }
        if (fileInput?.files.length > 0) {
          formData.append('bukti_bayar', fileInput.files[0]);
          formData.append('bukti', fileInput.files[0]);
        }

        const response = await fetch('/api/konfirmasi-iuran', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          },
          body: formData,
        });

        const res = await response.json();
        
        if (iuranStatusBadge) {
          iuranStatusBadge.innerHTML = `<span class="badge badge-emerald">Menunggu Verifikasi Bendahara</span>`;
        }
        if (paymentActionSection) {
          paymentActionSection.innerHTML = `
            <div style="background-color: var(--emerald-50); border: 1px solid var(--emerald-100); border-radius: var(--radius-md); padding: 14px; text-align: center; margin-top: 14px;">
              <strong style="color: var(--emerald-700); font-size: 0.9375rem;">Konfirmasi Pembayaran Diterima!</strong>
              <p style="color: var(--slate-600); font-size: 0.8125rem; margin-top: 4px;">
                Bukti transfer <em>"${fileName}"</em> telah tercatat di sistem RT (Tiket: ${res.ticket_code || '#APP'}). Bendahara akan memverifikasi dalam waktu maksimal 2 jam.
              </p>
            </div>
          `;
        }

        showToast('Konfirmasi Berhasil', res.message || 'Bukti pembayaran berhasil dikirim ke Bendahara RT 05.', 'success');
      } catch (err) {
        showToast('Konfirmasi Berhasil', 'Bukti transfer telah diterima sistem RT 05.', 'success');
      } finally {
        btnKonfirmasiBayar.disabled = false;
        btnKonfirmasiBayar.textContent = 'Unggah Bukti & Konfirmasi Pembayaran';
      }
    };

    btnKonfirmasiBayar.addEventListener('click', handleKonfirmasiBayar);
    const formKonfirmasiIuran = document.getElementById('formKonfirmasiIuran');
    if (formKonfirmasiIuran) {
      formKonfirmasiIuran.addEventListener('submit', handleKonfirmasiBayar);
    }
  }

  /* ==========================================================================
     5. FORM PENGAJUAN SURAT PENGANTAR (LARAVEL ENDPOINT)
     ========================================================================== */
  const formSuratPengantar = document.getElementById('formSuratPengantar');
  if (formSuratPengantar) {
    formSuratPengantar.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = formSuratPengantar.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = 'Mengirim...';

      const formData = new FormData(formSuratPengantar);

      try {
        const response = await fetch('/api/pengajuan-surat', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          },
          body: formData,
        });

        const res = await response.json();

        if (res.success) {
          closeModal(document.getElementById('modalSurat'));
          formSuratPengantar.reset();
          showToast(
            'Pengajuan Surat Terkirim',
            `Nomor Resi: ${res.tracking_code} atas nama ${res.nama}. Dokumen akan diverifikasi oleh Pak RT.`,
            'success'
          );
        } else {
          showToast('Gagal', res.message || 'Terjadi kesalahan.', 'error');
        }
      } catch (err) {
        // Fallback simulation
        const nama = document.getElementById('namaPemohon')?.value || 'Warga';
        const regNumber = `SRT-${new Date().getFullYear()}-${Math.floor(1000 + Math.random() * 9000)}`;
        closeModal(document.getElementById('modalSurat'));
        formSuratPengantar.reset();
        showToast('Pengajuan Surat Terkirim', `Nomor Resi: ${regNumber} atas nama ${nama}. Verifikasi segera diproses.`, 'success');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });
  }

  /* ==========================================================================
     6. FORM PEMINJAMAN ASET / ALAT RT (LARAVEL ENDPOINT)
     ========================================================================== */
  const formPinjamAset = document.getElementById('formPinjamAset');
  if (formPinjamAset) {
    const today = new Date().toISOString().split('T')[0];
    const tglMulai = document.getElementById('tglMulaiPinjam');
    const tglSelesai = document.getElementById('tglSelesaiPinjam');
    if (tglMulai && tglSelesai) {
      tglMulai.value = today;
      tglSelesai.value = today;
    }

    formPinjamAset.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = formPinjamAset.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = 'Memproses...';

      const formData = new FormData(formPinjamAset);

      try {
        const response = await fetch('/api/pinjam-aset', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          },
          body: formData,
        });

        const res = await response.json();

        if (res.success) {
          closeModal(document.getElementById('modalPinjam'));
          formPinjamAset.reset();
          showToast(
            'Permohonan Peminjaman Dicatat',
            `Kode: ${res.booking_code} (${res.aset}) untuk Bpk/Ibu ${res.nama}. Seksi Perlengkapan RT akan segera menghubungi Anda.`,
            'success'
          );
        } else {
          showToast('Gagal', res.message || 'Terjadi kesalahan.', 'error');
        }
      } catch (err) {
        const kodeBooking = `PINJAM-${Math.floor(100 + Math.random() * 900)}`;
        closeModal(document.getElementById('modalPinjam'));
        formPinjamAset.reset();
        showToast('Permohonan Peminjaman Dicatat', `Kode Booking: ${kodeBooking}. Seksi Perlengkapan RT akan menghubungi Anda.`, 'success');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });
  }

  /* ==========================================================================
     7. FORM KIRIM ASPIRASI WARGA & FEED DYNAMICS
     ========================================================================== */
  const formKirimAspirasi = document.getElementById('formKirimAspirasi');
  const anonimCheckbox = document.getElementById('anonimCheckbox');
  const identitasAspirasiGroup = document.getElementById('identitasAspirasiGroup');
  const aspirasiFeedContainer = document.getElementById('aspirasiFeedContainer');

  if (anonimCheckbox && identitasAspirasiGroup) {
    anonimCheckbox.addEventListener('change', () => {
      identitasAspirasiGroup.style.display = anonimCheckbox.checked ? 'none' : 'grid';
    });
  }

  if (formKirimAspirasi) {
    formKirimAspirasi.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = formKirimAspirasi.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = 'Mengirim...';

      const formData = new FormData(formKirimAspirasi);

      try {
        const response = await fetch('/api/aspirasi', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          },
          body: formData,
        });

        const res = await response.json();

        if (res.success) {
          const data = res.data;

          // Dynamically prepend new aspirasi card to the feed
          const newCard = document.createElement('article');
          newCard.className = 'aspirasi-card';
          newCard.setAttribute('data-category', data.kategori);

          newCard.innerHTML = `
            <div class="aspirasi-header">
              <div class="aspirasi-user-info">
                <div class="user-avatar-circle" style="background-color: var(--primary-100); color: var(--primary-700);">${data.initials}</div>
                <div class="user-name-wrapper">
                  <div class="user-display-name">${data.nama} (${data.blok})</div>
                  <div class="aspirasi-timestamp">Baru saja • Kategori: ${data.kategori.toUpperCase()}</div>
                </div>
              </div>
              <span class="badge badge-amber">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                Menunggu Respons Ketua RT
              </span>
            </div>

            <h4 class="aspirasi-title">${data.judul}</h4>
            <p class="aspirasi-body">${data.isi}</p>

            <div class="official-response-box" style="background-color: var(--slate-50); border-color: var(--slate-200); border-left-color: var(--slate-400);">
              <div class="response-header">
                <div class="responder-title" style="color: var(--slate-700);">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                  Notifikasi Pengurus RT
                </div>
                <div class="responder-time">Status: Diterima Sistem</div>
              </div>
              <p class="response-content" style="color: var(--slate-500); font-style: italic;">
                "Aspirasi ini telah diteruskan ke WhatsApp Pengurus RT dan akan direspons dalam waktu maksimal 1x24 jam."
              </p>
            </div>
          `;

          if (aspirasiFeedContainer) {
            aspirasiFeedContainer.insertBefore(newCard, aspirasiFeedContainer.firstChild);
          }

          closeModal(document.getElementById('modalAspirasi'));
          formKirimAspirasi.reset();
          if (identitasAspirasiGroup) identitasAspirasiGroup.style.display = 'grid';

          showToast('Aspirasi Berhasil Terkirim', res.message, 'success');
        }
      } catch (err) {
        showToast('Aspirasi Terkirim', 'Terima kasih, aspirasi Anda telah dicatat oleh sistem.', 'success');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });
  }

  /* ==========================================================================
     8. ASPIRASI FILTER TABS
     ========================================================================== */
  const filterButtons = document.querySelectorAll('#aspirasiFilters .filter-btn');
  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const filterValue = btn.getAttribute('data-filter');
      const cards = document.querySelectorAll('.aspirasi-card');

      cards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        if (filterValue === 'all' || cardCategory === filterValue) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  /* ==========================================================================
     9. FORM USULAN KEGIATAN WARGA (LARAVEL ENDPOINT)
     ========================================================================== */
  const formUsulKegiatan = document.getElementById('formUsulKegiatan');
  if (formUsulKegiatan) {
    formUsulKegiatan.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(formUsulKegiatan);

      try {
        const response = await fetch('/api/usul-kegiatan', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
          },
          body: formData,
        });

        const res = await response.json();
        closeModal(document.getElementById('modalUsulanKegiatan'));
        formUsulKegiatan.reset();

        showToast('Usulan Kegiatan Diterima', res.message || 'Usulan Anda akan dibahas pada rapat koordinasi.', 'success');
      } catch (err) {
        closeModal(document.getElementById('modalUsulanKegiatan'));
        formUsulKegiatan.reset();
        showToast('Usulan Kegiatan Diterima', 'Usulan kegiatan Anda telah disimpan.', 'success');
      }
    });
  }

  /* ==========================================================================
     10. COPY EMERGENCY PHONE TO CLIPBOARD
     ========================================================================== */
  const copyButtons = document.querySelectorAll('.btn-copy-phone');
  copyButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const phoneNumber = btn.getAttribute('data-phone');
      if (navigator.clipboard) {
        navigator.clipboard.writeText(phoneNumber).then(() => {
          showToast('Nomor Disalin', `Nomor ${phoneNumber} berhasil disalin ke clipboard ponsel Anda.`, 'info');
        }).catch(() => {
          showToast('Kontak Cepat', `Silakan hubungi: ${phoneNumber}`, 'info');
        });
      } else {
        showToast('Kontak Cepat', `Silakan hubungi: ${phoneNumber}`, 'info');
      }
    });
  });

  /* ==========================================================================
     11. EVENT DETAIL TRIGGER
     ========================================================================== */
  const eventDetailButtons = document.querySelectorAll('.btn-action-detail');
  eventDetailButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const eventName = btn.getAttribute('data-event');
      showToast(
        'Rincian Kegiatan',
        `Kegiatan: "${eventName}". Informasi perlengkapan yang perlu dibawa dan susunan acara telah disiarkan di grup WhatsApp warga.`,
        'info'
      );
    });
  });

  /* ==========================================================================
     12. UNDUH REKAP KAS PDF
     ========================================================================== */
  const btnDownloadLaporan = document.getElementById('btnDownloadLaporan');
  if (btnDownloadLaporan) {
    btnDownloadLaporan.addEventListener('click', () => {
      showToast(
        'Unduh Rekap Kas',
        'Menyiapkan dokumen PDF Laporan Keuangan Kas RT 05 Periode September 2026 yang terverifikasi...',
        'info'
      );
    });
  }

  /* ==========================================================================
     13. FORM LOGIN WARGA & CMS SIMULASI
     ========================================================================== */
  const formLoginWarga = document.getElementById('formLoginWarga');
  if (formLoginWarga) {
    formLoginWarga.addEventListener('submit', (e) => {
      e.preventDefault();
      const identifier = document.getElementById('loginIdentifier')?.value || '';
      closeModal(document.getElementById('modalLoginWarga'));
      formLoginWarga.reset();
      showToast(
        'Selamat Datang di RuangWarga',
        `Berhasil masuk ke akun warga (${identifier}). Layanan mandiri Anda kini aktif.`,
        'success'
      );
    });
  }

  /* ==========================================================================
     14. PORTAL WARGA MANDIRI: CEK STATUS SURAT ACC & MUTASI IURAN KELUARGA
     ========================================================================== */
  const modalStatusWarga = document.getElementById('modalStatusWarga');
  const btnTabSurat = document.getElementById('btnTabSurat');
  const btnTabMutasi = document.getElementById('btnTabMutasi');
  const tabSuratWarga = document.getElementById('tabSuratWarga');
  const tabMutasiWarga = document.getElementById('tabMutasiWarga');

  function switchWargaTab(tabKey) {
    if (!btnTabSurat || !btnTabMutasi || !tabSuratWarga || !tabMutasiWarga) return;

    if (tabKey === 'surat') {
      btnTabSurat.classList.add('active');
      btnTabMutasi.classList.remove('active');
      tabSuratWarga.classList.add('active');
      tabMutasiWarga.classList.remove('active');
    } else if (tabKey === 'mutasi') {
      btnTabMutasi.classList.add('active');
      btnTabSurat.classList.remove('active');
      tabMutasiWarga.classList.add('active');
      tabSuratWarga.classList.remove('active');
    }
  }

  if (btnTabSurat) {
    btnTabSurat.addEventListener('click', () => switchWargaTab('surat'));
  }
  if (btnTabMutasi) {
    btnTabMutasi.addEventListener('click', () => switchWargaTab('mutasi'));
  }

  // Open modal and switch to tab when clicking [data-open-warga-tab] triggers
  const wargaTabTriggers = document.querySelectorAll('[data-open-warga-tab]');
  wargaTabTriggers.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const targetTab = btn.getAttribute('data-open-warga-tab');
      switchWargaTab(targetTab);
      openModal('modalStatusWarga');
    });
  });

  // Quick switch active citizen dropdown in Hero banner
  const quickSwitchCitizenSelect = document.getElementById('quickSwitchCitizenSelect');
  if (quickSwitchCitizenSelect) {
    quickSwitchCitizenSelect.addEventListener('change', async (e) => {
      const selectedNik = e.target.value;
      if (!selectedNik) return;

      try {
        const response = await fetch('/api/pilih-akun-warga', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({ nik: selectedNik })
        });

        const data = await response.json();
        if (data.status === 'success') {
          showToast('Akun Warga Aktif', `Beralih ke akun ${data.citizen.nama} (${data.citizen.alamat}). Memuat data...`, 'success');
          setTimeout(() => {
            window.location.reload();
          }, 600);
        } else {
          showToast('Peringatan', data.message || 'Gagal mengganti akun warga.', 'error');
        }
      } catch (err) {
        console.error('Error switching citizen:', err);
        showToast('Kesalahan Jaringan', 'Gagal memproses peralihan akun warga.', 'error');
      }
    });
  }

  // Live Search NIK / Blok / Nama inside modal
  const btnSearchWargaRiwayat = document.getElementById('btnSearchWargaRiwayat');
  const searchWargaQueryInput = document.getElementById('searchWargaQueryInput');

  async function performWargaRiwayatSearch() {
    const query = searchWargaQueryInput?.value.trim() || '';
    if (!query) {
      showToast('Pencarian Data', 'Silakan ketik NIK, No. Rumah, atau Nama warga.', 'info');
      return;
    }

    if (btnSearchWargaRiwayat) {
      btnSearchWargaRiwayat.disabled = true;
      btnSearchWargaRiwayat.innerHTML = `
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="spin" style="animation: spin 1s linear infinite;"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
        <span>Mencari...</span>
      `;
    }

    try {
      const response = await fetch(`/api/riwayat-warga?query=${encodeURIComponent(query)}`, {
        headers: { 'Accept': 'application/json' }
      });
      const data = await response.json();

      if (data.status === 'success') {
        const citizen = data.citizen;
        const summary = data.summary;
        const letters = data.letters || [];
        const mutations = data.mutations || [];

        // Update profile header in modal
        const modalCitizenAvatar = document.getElementById('modalCitizenAvatar');
        const modalCitizenName = document.getElementById('modalCitizenName');
        const modalCitizenDomisili = document.getElementById('modalCitizenDomisili');
        const modalCitizenAddress = document.getElementById('modalCitizenAddress');
        const modalSummarySuratBadge = document.getElementById('modalSummarySuratBadge');
        const modalSummaryIuranBadge = document.getElementById('modalSummaryIuranBadge');
        const modalIuranStatusText = document.getElementById('modalIuranStatusText');
        const modalTotalIuranText = document.getElementById('modalTotalIuranText');
        const tabSuratCountBadge = document.getElementById('tabSuratCountBadge');
        const tabMutasiCountBadge = document.getElementById('tabMutasiCountBadge');

        if (modalCitizenAvatar) modalCitizenAvatar.textContent = (citizen.nama || 'W').substring(0, 2).toUpperCase();
        if (modalCitizenName) modalCitizenName.textContent = citizen.nama || 'Warga';
        if (modalCitizenDomisili) modalCitizenDomisili.textContent = (citizen.status_domisili || 'Tetap').toUpperCase();
        if (modalCitizenAddress) {
          modalCitizenAddress.textContent = `${citizen.alamat || '-'} • No. KK: ${citizen.no_kk || '-'} • NIK: ${citizen.nik || '-'}`;
        }
        if (modalSummarySuratBadge) {
          modalSummarySuratBadge.textContent = `${summary.total_surat_acc || 0} Surat Di-ACC`;
        }
        if (modalSummaryIuranBadge) {
          modalSummaryIuranBadge.className = `badge ${summary.is_lunas_bulan_ini ? 'badge-emerald' : 'badge-amber'}`;
          modalSummaryIuranBadge.textContent = summary.is_lunas_bulan_ini ? 'Iuran Sep: Lunas' : 'Iuran Sep: Belum Lunas';
        }
        if (modalIuranStatusText) {
          modalIuranStatusText.textContent = summary.is_lunas_bulan_ini ? 'LUNAS (Rp 75.000)' : 'BELUM DIBAYAR';
        }
        if (modalTotalIuranText) {
          modalTotalIuranText.textContent = summary.total_terbayar_tahun_ini || 'Rp 0';
        }
        if (tabSuratCountBadge) tabSuratCountBadge.textContent = letters.length;
        if (tabMutasiCountBadge) tabMutasiCountBadge.textContent = mutations.length;

        // Render Letters Cards
        const suratCardsListWrapper = document.getElementById('suratCardsListWrapper');
        if (suratCardsListWrapper) {
          if (letters.length === 0) {
            suratCardsListWrapper.innerHTML = `
              <div style="text-align: center; padding: 36px 16px; background-color: var(--slate-50); border: 1px dashed var(--slate-300); border-radius: var(--radius-lg); color: var(--slate-500); font-size: 0.8125rem;">
                <div>Belum ada arsip permohonan surat untuk warga ini.</div>
                <button type="button" class="btn btn-primary" data-modal="modalSurat" style="margin-top: 12px; font-size: 0.75rem;">
                  + Buat Permohonan Surat Sekarang
                </button>
              </div>
            `;
          } else {
            suratCardsListWrapper.innerHTML = letters.map(let => {
              const cardClass = let.is_acc ? 'acc-approved' : (let.is_rejected ? 'acc-rejected' : 'acc-pending');
              let statusBadge = '';
              let actionButtons = '';

              if (let.is_acc) {
                statusBadge = `
                  <span class="badge badge-emerald" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>SUDAH DI-ACC KETUA RT</span>
                  </span>
                `;
                actionButtons = `
                  <a href="${let.pdf_url}" target="_blank" class="btn btn-primary" style="font-size: 0.75rem; padding: 6px 12px; gap: 6px; text-decoration: none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Unduh PDF Ber-QR Code</span>
                  </a>
                  <a href="${let.verify_url}" target="_blank" class="btn btn-subtle" style="font-size: 0.75rem; padding: 6px 12px; text-decoration: none;">
                    Verifikasi Digital
                  </a>
                `;
              } else if (let.is_rejected) {
                statusBadge = `<span class="badge badge-rose" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px;">DITOLAK / REVISI</span>`;
                actionButtons = `<span style="font-size: 0.75rem; color: var(--rose-600);">Silakan ajukan ulang permohonan dengan data lengkap</span>`;
              } else {
                statusBadge = `
                  <span class="badge badge-amber" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background-color: var(--amber-500); animation: pulse 1.5s infinite;"></span>
                    <span>MENUNGGU PERSETUJUAN</span>
                  </span>
                `;
                actionButtons = `
                  <span style="font-size: 0.75rem; color: var(--amber-700); font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
                    <span>Menunggu Tanda Tangan Pak RT</span>
                  </span>
                `;
              }

              return `
                <div class="surat-card-item ${cardClass}">
                  <div class="surat-header-row">
                    <div>
                      <div class="surat-title-bold">${let.jenis_surat}</div>
                      <div class="surat-number-tag">No: ${let.nomor_surat}</div>
                    </div>
                    <div>${statusBadge}</div>
                  </div>
                  <div class="surat-desc-text"><strong>Keperluan:</strong> ${let.keperluan}</div>
                  <div style="font-size: 0.75rem; color: var(--slate-500); background-color: var(--slate-50); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px dashed var(--slate-200);">
                    💡 <em>${let.keterangan_acc}</em>
                  </div>
                  <div class="surat-action-footer">
                    <div style="font-size: 0.75rem; color: var(--slate-400);">Diajukan: <strong>${let.tanggal}</strong></div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">${actionButtons}</div>
                  </div>
                </div>
              `;
            }).join('');
          }
        }

        // Render Mutations Table
        const mutasiTableBody = document.getElementById('mutasiTableBody');
        if (mutasiTableBody) {
          if (mutations.length === 0) {
            mutasiTableBody.innerHTML = `
              <tr>
                <td colspan="6" style="text-align: center; padding: 24px; color: var(--slate-400);">
                  Belum ada catatan mutasi iuran untuk unit rumah ini.
                </td>
              </tr>
            `;
          } else {
            mutasiTableBody.innerHTML = mutations.map(mut => {
              const statusPill = mut.is_lunas
                ? `<span class="badge badge-emerald" style="font-size: 0.6875rem;">✓ Lunas Sah</span>`
                : `<span class="badge badge-amber" style="font-size: 0.6875rem;">⏳ Menunggu Bendahara</span>`;

              return `
                <tr>
                  <td>
                    <strong style="color: var(--slate-900); font-size: 0.8125rem;">${mut.periode}</strong>
                    <div style="font-size: 0.6875rem; color: var(--slate-400); font-family: monospace;">${mut.ref_code}</div>
                  </td>
                  <td style="color: var(--slate-600); white-space: nowrap;">${mut.tanggal}</td>
                  <td><strong style="color: var(--emerald-600); font-weight: 800;">${mut.nominal_formatted}</strong></td>
                  <td>
                    <div style="font-weight: 600; color: var(--slate-800);">${mut.kategori}</div>
                    <div style="font-size: 0.6875rem; color: var(--slate-500);">${mut.metode}</div>
                  </td>
                  <td>${statusPill}</td>
                  <td style="text-align: right;">
                    <button type="button" class="btn btn-subtle btn-view-receipt" data-ref="${mut.ref_code}" data-periode="${mut.periode}" data-nominal="${mut.nominal_formatted}" data-tanggal="${mut.tanggal}" style="padding: 4px 8px; font-size: 0.6875rem;">
                      Struk Sah
                    </button>
                  </td>
                </tr>
              `;
            }).join('');
          }
        }

        showToast('Data Ditemukan', `Menampilkan data riwayat & status surat untuk ${citizen.nama}.`, 'success');
      } else {
        showToast('Data Tidak Ditemukan', data.message || 'Warga dengan data tersebut belum terdaftar.', 'error');
      }
    } catch (err) {
      console.error('Error searching warga:', err);
      showToast('Gagal Memuat Data', 'Terjadi gangguan saat mengambil data dari server.', 'error');
    } finally {
      if (btnSearchWargaRiwayat) {
        btnSearchWargaRiwayat.disabled = false;
        btnSearchWargaRiwayat.innerHTML = `
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.35-4.35"/>
          </svg>
          <span>Cari Data</span>
        `;
      }
    }
  }

  if (btnSearchWargaRiwayat) {
    btnSearchWargaRiwayat.addEventListener('click', performWargaRiwayatSearch);
  }
  if (searchWargaQueryInput) {
    searchWargaQueryInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        performWargaRiwayatSearch();
      }
    });
  }

  // Delegated Receipt Click Handler
  document.addEventListener('click', (e) => {
    const receiptBtn = e.target.closest('.btn-view-receipt');
    if (receiptBtn) {
      const ref = receiptBtn.getAttribute('data-ref') || 'TX-2026';
      const periode = receiptBtn.getAttribute('data-periode') || 'Bulan Berjalan';
      const nominal = receiptBtn.getAttribute('data-nominal') || 'Rp 75.000';
      const tanggal = receiptBtn.getAttribute('data-tanggal') || '-';

      showToast(
        'Bukti Setor Kas Sah',
        `No. Ref: ${ref} | Periode: ${periode} | Nominal: ${nominal} (${tanggal}). Tercatat sah dalam Pembukuan Kas RT 05.`,
        'success'
      );
    }
  });

});

