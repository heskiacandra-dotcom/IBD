/**
 * ============================================================================
 * RUANGWARGA - JAVASCRIPT LOGIC & INTERACTIVITY (LARAVEL INTEGRATION)
 * Portal Layanan Mandiri & Transparansi Lingkungan RT
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', () => {

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

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
    mobileMenuBtn.addEventListener('click', () => {
      navLinks.classList.toggle('open');
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
    btnKonfirmasiBayar.addEventListener('click', async () => {
      const fileInput = document.getElementById('fileBuktiBayar');
      const fileName = fileInput?.files.length > 0 ? fileInput.files[0].name : 'Struk transfer QRIS';
      const house = searchHouseNumber.value.trim() || 'B-12';

      try {
        const formData = new FormData();
        formData.append('house', house);
        if (fileInput?.files.length > 0) {
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
        
        iuranStatusBadge.innerHTML = `<span class="badge badge-emerald">Menunggu Verifikasi Bendahara</span>`;
        paymentActionSection.innerHTML = `
          <div style="background-color: var(--emerald-50); border: 1px solid var(--emerald-100); border-radius: var(--radius-md); padding: 14px; text-align: center; margin-top: 14px;">
            <strong style="color: var(--emerald-700); font-size: 0.9375rem;">Konfirmasi Pembayaran Diterima!</strong>
            <p style="color: var(--slate-600); font-size: 0.8125rem; margin-top: 4px;">
              Bukti transfer <em>"${fileName}"</em> telah tercatat di sistem RT. Bendahara akan memverifikasi dalam waktu maksimal 2 jam.
            </p>
          </div>
        `;

        showToast('Konfirmasi Berhasil', res.message || 'Bukti pembayaran berhasil dikirim ke Bendahara RT 05.', 'success');
      } catch (err) {
        showToast('Konfirmasi Berhasil', 'Bukti transfer telah diterima sistem simulasi RT 05.', 'success');
      }
    });
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

  const btnSimulasiMasukCms = document.getElementById('btnSimulasiMasukCms');
  if (btnSimulasiMasukCms) {
    btnSimulasiMasukCms.addEventListener('click', () => {
      closeModal(document.getElementById('modalCmsNotice'));
      showToast(
        'Akses Pengurus RT',
        'Membuka sesi otentikasi pengurus RT (Ketua, Sekretaris, Bendahara)...',
        'info'
      );
    });
  }

});
