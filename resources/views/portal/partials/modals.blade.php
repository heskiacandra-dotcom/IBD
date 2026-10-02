<!-- ==========================================================================
     MODAL: SURAT PENGANTAR ONLINE (calls sp_ajukan_surat_warga)
     ========================================================================== -->
<div class="modal-overlay" id="modalSurat" role="dialog" aria-modal="true">
  <div class="modal-container">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Surat Pengantar Online</h3>
          <div class="modal-subtitle">Proses cepat verifikasi resmi RT via stored procedure `sp_ajukan_surat_warga`</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formSuratPengantar" action="{{ route('portal.submit-surat') }}" method="POST">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Jenis Surat Pengantar <span class="req">*</span></label>
          <select class="form-select" id="jenisSurat" name="jenis_surat" required>
            <option value="">-- Pilih Jenis Surat Pengantar --</option>
            <option value="Surat Keterangan Domisili Warga">Surat Keterangan Domisili Warga</option>
            <option value="Surat Pengantar Pembuatan / Perubahan KTP & KK">Surat Pengantar Pembuatan / Perubahan KTP & KK</option>
            <option value="Surat Pengantar SKCK Kepolisian">Surat Pengantar SKCK Kepolisian</option>
            <option value="Surat Keterangan Usaha / UMKM Warga">Surat Keterangan Usaha / UMKM Warga</option>
            <option value="Surat Keterangan Belum Menikah / Menikah">Surat Keterangan Belum Menikah / Menikah</option>
            <option value="Surat Keterangan Kurang Mampu (Beasiswa/Bansos)">Surat Keterangan Kurang Mampu (Beasiswa/Bansos)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Nama Lengkap Pemohon (Sesuai KTP) <span class="req">*</span></label>
          <input type="text" class="form-input" id="namaPemohon" name="nama" value="{{ $warga->Nama_Lengkap ?? ($activeCitizen->nama ?? '') }}" placeholder="Contoh: Hendra Wijaya" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Nomor Induk Kependudukan (NIK) <span class="req">*</span></label>
            <input type="text" class="form-input" id="nikPemohon" name="nik" value="{{ $warga->NIK ?? ($activeCitizen->nik ?? '') }}" placeholder="16 digit NIK" maxlength="16" required>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Rumah / Blok <span class="req">*</span></label>
            <input type="text" class="form-input" id="blokPemohon" name="blok" value="{{ optional($warga->kartuKeluarga ?? null)->Alamat ?? ($activeCitizen->alamat ?? '') }}" placeholder="Misal: Blok B-12" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor WhatsApp Aktif <span class="req">*</span></label>
          <input type="tel" class="form-input" id="waPemohon" name="no_wa" value="{{ $warga->no_telp ?? ($activeCitizen->whatsapp ?? '081234567890') }}" placeholder="08xxxxxxxxxx (untuk notifikasi surat)" required>
          <div class="form-hint">Surat yang disetujui akan terbit otomatis dengan QR Code dan dapat diunduh langsung.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Keperluan Pengajuan Surat <span class="req">*</span></label>
          <textarea class="form-textarea" id="keperluanSurat" name="keperluan" placeholder="Jelaskan secara singkat keperluan Anda, misal: Persyaratan administrasi pekerjaan / Perpanjangan dokumen..." required></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitSurat">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
          Kirim Pengajuan Surat
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: BAYAR IURAN BULANAN (Checks pending bills from PEMASUKAN & allows receipt upload)
     ========================================================================== -->
<div class="modal-overlay" id="modalIuran" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 680px;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--emerald-50); color: var(--emerald-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Bayar Iuran Bulanan</h3>
          <div class="modal-subtitle">Pengecekan tagihan kas dari tabel PEMASUKAN & upload bukti transfer</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formKonfirmasiIuran" action="{{ route('portal.konfirmasi-iuran') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="modal-body">
        <!-- Household ID & Privacy Scope Info -->
        <input type="hidden" id="searchHouseNumber" name="house" value="{{ optional($warga->kartuKeluarga ?? null)->Alamat ?? ($activeCitizen->alamat ?? 'Harmoni') }}">
        
        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.75rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Tagihan Khusus Kartu Keluarga:</div>
            <div style="font-size: 0.9375rem; font-weight: 800; color: #0F172A;">
              KK: {{ $warga->ID_KK ?? ($activeCitizen->no_kk ?? '-') }} • {{ $warga->Nama_Lengkap ?? ($activeCitizen->nama ?? 'Warga') }}
            </div>
          </div>
          <span style="font-size: 0.6875rem; font-weight: 700; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; padding: 3px 8px; border-radius: 9999px;">
            Data Terenkripsi & Terisolasi
          </span>
        </div>

        <!-- Pending Bills List from PEMASUKAN -->
        @if(isset($tagihanPending) && count($tagihanPending) > 0)
          <div style="margin-bottom: 16px;">
            <label class="form-label" style="font-weight: 700; color: var(--slate-800);">Pilih Tagihan yang Ingin Dibayar</label>
            <div class="pending-bills-list" style="display: flex; flex-direction: column; gap: 8px; max-height: 180px; overflow-y: auto; border: 1px solid var(--slate-200); border-radius: var(--radius-md); padding: 8px; background-color: var(--slate-50);">
              @foreach($tagihanPending as $tp)
                <label style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #ffffff; border: 1px solid var(--slate-200); border-radius: var(--radius-sm); cursor: pointer;">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="radio" name="tagihan_id" value="{{ $tp->Tagihan_Id }}" data-nominal="{{ $tp->nominal }}" {{ $loop->first ? 'checked' : '' }}>
                    <div>
                      <strong style="font-size: 0.8125rem; color: var(--slate-900);">{{ $tp->nama_tagihan }}</strong>
                      <div style="font-size: 0.6875rem; color: var(--slate-500);">Jatuh Tempo: {{ \Illuminate\Support\Carbon::parse($tp->tgl_jatuh_tmp)->translatedFormat('d M Y') }}</div>
                    </div>
                  </div>
                  <div style="text-align: right;">
                    <span style="font-size: 0.875rem; font-weight: 800; color: var(--rose-600);">Rp {{ number_format($tp->nominal, 0, ',', '.') }}</span>
                    <div>
                      <span class="badge {{ in_array(strtolower($tp->status_tagihan), ['pending_approval', 'menunggu verifikasi', 'menunggu']) ? 'badge-amber' : 'badge-rose' }}" style="font-size: 0.625rem;">
                        {{ in_array(strtolower($tp->status_tagihan), ['pending_approval', 'menunggu verifikasi', 'menunggu']) ? 'Menunggu Verifikasi' : 'Belum Dibayar' }}
                      </span>
                    </div>
                  </div>
                </label>
              @endforeach
            </div>
          </div>
        @else
          <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; text-align: center;">
            <strong style="color: #047857; font-size: 0.875rem;">Semua Tagihan Keluarga Anda Lunas!</strong>
            <p style="color: #065F46; font-size: 0.75rem; margin: 4px 0 0 0;">Tidak ada tagihan tertunggak pada pembukuan kas RT saat ini.</p>
          </div>
        @endif

        <!-- Result Box & Payment Simulator -->
        <div class="iuran-result-box" id="iuranResultBox" style="display: block;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
              <strong id="iuranOwnerName" style="font-size: 1rem; color: var(--slate-900);">{{ $warga->Nama_Lengkap ?? ($activeCitizen->nama ?? 'Warga RT 05') }}</strong>
              <div id="iuranHouseAddress" style="font-size: 0.8125rem; color: var(--slate-500);">{{ optional($warga->kartuKeluarga ?? null)->Alamat ?? ($activeCitizen->alamat ?? 'Harmoni Residence') }}</div>
            </div>
            <div id="iuranStatusBadge">
              @if(isset($tagihanPending) && count($tagihanPending) > 0)
                <span class="badge badge-amber">Belum Lunas</span>
              @else
                <span class="badge badge-emerald">Lunas</span>
              @endif
            </div>
          </div>

          <table class="iuran-details-table">
            <tbody>
              <tr>
                <td>Iuran Keamanan & Ronda</td>
                <td>Rp 45.000</td>
              </tr>
              <tr>
                <td>Iuran Kebersihan & Pengangkutan Sampah</td>
                <td>Rp 30.000</td>
              </tr>
              <tr style="font-weight: 700; color: var(--slate-900); font-size: 0.9375rem;">
                <td>Total Tagihan Berjalan</td>
                <td id="iuranTotalAmount" style="color: var(--primary-600); font-weight: 800;">Rp 75.000</td>
              </tr>
            </tbody>
          </table>

          <!-- QRIS & Transfer simulator -->
          <div id="paymentActionSection">
            <div class="qris-demo-area">
              <span class="badge badge-emerald" style="margin-bottom: 8px;">QRIS Kas RT 05 (NMID: ID1029384756)</span>
              <div class="qris-mock-code">
                <svg viewBox="0 0 100 100" width="110" height="110">
                  <rect x="0" y="0" width="30" height="30" fill="#1E293B"/>
                  <rect x="5" y="5" width="20" height="20" fill="#FFFFFF"/>
                  <rect x="10" y="10" width="10" height="10" fill="#1E293B"/>
                  <rect x="70" y="0" width="30" height="30" fill="#1E293B"/>
                  <rect x="75" y="5" width="20" height="20" fill="#FFFFFF"/>
                  <rect x="80" y="10" width="10" height="10" fill="#1E293B"/>
                  <rect x="0" y="70" width="30" height="30" fill="#1E293B"/>
                  <rect x="5" y="75" width="20" height="20" fill="#FFFFFF"/>
                  <rect x="10" y="80" width="10" height="10" fill="#1E293B"/>
                  <rect x="40" y="10" width="15" height="15" fill="#1E293B"/>
                  <rect x="45" y="35" width="25" height="25" fill="#2563EB"/>
                  <rect x="15" y="45" width="15" height="15" fill="#1E293B"/>
                  <rect x="70" y="45" width="20" height="15" fill="#1E293B"/>
                  <rect x="40" y="75" width="20" height="15" fill="#1E293B"/>
                  <rect x="75" y="75" width="15" height="15" fill="#1E293B"/>
                </svg>
              </div>
              <div style="font-size: 0.75rem; color: var(--slate-600); margin-top: 6px;">
                BCA, Mandiri, BRI, BSI, GoPay, OVO, Dana, ShopeePay.
              </div>
            </div>

            <!-- Upload Payment Receipt Form -->
            <div style="margin-top: 16px; background-color: #FFFFFF; border: 1px dashed var(--slate-300); border-radius: var(--radius-md); padding: 14px;">
              <label class="form-label" style="font-weight: 700;">Unggah Struk / Bukti Transfer <span class="req">*</span></label>
              <input type="file" class="form-input" id="fileBuktiBayar" name="bukti_bayar" accept="image/*,application/pdf">
              <div class="form-hint">Mendukung format gambar (JPG, PNG) atau PDF maksimal 5MB.</div>
              
              <button type="submit" class="btn btn-success" id="btnKonfirmasiBayar" style="width: 100%; margin-top: 12px; font-weight: 700;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Unggah Bukti & Konfirmasi Pembayaran
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Tutup</button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: PINJAM INVENTARIS RT (Catalog view of INVENTARIS with stock status)
     ========================================================================== -->
<div class="modal-overlay" id="modalPinjam" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 760px;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--purple-50); color: var(--purple-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Pinjam Inventaris RT</h3>
          <div class="modal-subtitle">Katalog lengkap aset bersama balai warga dengan status ketersediaan stok</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formPinjamAset" action="{{ route('portal.pinjam-aset') }}" method="POST">
      @csrf
      <input type="hidden" id="selectedAsetNama" name="aset" value="{{ isset($inventarisCatalog) && count($inventarisCatalog) > 0 ? $inventarisCatalog[0]['nama'] : 'Tenda Lipat (3x6 Meter)' }}">

      <div class="modal-body">
        <div class="form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label class="form-label" style="font-weight: 700; margin: 0;">Katalog Inventaris RT & Status Stok <span class="req">*</span></label>
            <span class="badge badge-slate" style="font-size: 0.6875rem;">Pilih salah satu alat</span>
          </div>

          <!-- Catalog View Grid of INVENTARIS -->
          <div class="inventaris-catalog-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; max-height: 250px; overflow-y: auto; padding: 6px; border: 1px solid var(--slate-200); border-radius: var(--radius-md); background-color: var(--slate-50);">
            @forelse($inventarisCatalog ?? [] as $item)
              <label class="catalog-item-card" style="border: 1px solid var(--slate-200); border-radius: var(--radius-sm); padding: 12px; background: #ffffff; cursor: pointer; display: flex; flex-direction: column; gap: 6px; transition: all var(--transition-fast); box-shadow: var(--shadow-xs);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                  <input type="radio" name="inventaris_id" value="{{ $item['id'] }}" data-nama="{{ $item['nama'] }}" {{ $loop->first ? 'checked' : '' }} onchange="document.getElementById('selectedAsetNama').value = this.dataset.nama;">
                  <span class="badge {{ $item['status_badge'] }}" style="font-size: 0.6875rem;">{{ $item['status_label'] }}</span>
                </div>
                <strong style="font-size: 0.8125rem; color: var(--slate-900); line-height: 1.3;">{{ $item['nama'] }}</strong>
                <div style="font-size: 0.6875rem; color: var(--slate-500); display: flex; justify-content: space-between;">
                  <span>Kode: <code>{{ $item['kode'] }}</code></span>
                  <span>Stok: <strong>{{ $item['jumlah'] }}</strong></span>
                </div>
                <div style="font-size: 0.6875rem; color: var(--slate-400);">📍 {{ $item['lokasi'] }}</div>
              </label>
            @empty
              <div style="padding: 20px; text-align: center; color: var(--slate-400); grid-column: 1 / -1;">
                Data inventaris belum tersedia di sistem.
              </div>
            @endforelse
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Tanggal Mulai Pakai <span class="req">*</span></label>
            <input type="date" class="form-input" id="tglMulaiPinjam" name="tgl_mulai" required>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Pengembalian <span class="req">*</span></label>
            <input type="date" class="form-input" id="tglSelesaiPinjam" name="tgl_selesai" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Nama Peminjam <span class="req">*</span></label>
            <input type="text" class="form-input" id="namaPeminjam" name="nama" value="{{ $activeCitizen->nama ?? '' }}" placeholder="Nama Warga" required>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Rumah / Blok <span class="req">*</span></label>
            <input type="text" class="form-input" id="blokPeminjam" name="blok" value="{{ $activeCitizen->alamat ?? '' }}" placeholder="Contoh: A-03" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Tujuan Pemakaian <span class="req">*</span></label>
          <input type="text" class="form-input" id="tujuanPinjam" name="tujuan" placeholder="Misal: Acara syukuran aqiqah / Arisan warga / Duka cita..." required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" style="background-color: var(--purple-600);" id="btnSubmitPinjam">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Ajukan Peminjaman Inventaris
        </button>
      </div>
    </form>
  </div>
</div>


<!-- ==========================================================================
     MODAL: SAMPAIKAN ASPIRASI (Submits to ASPIRASI table)
     ========================================================================== -->
<div class="modal-overlay" id="modalAspirasi" role="dialog" aria-modal="true">
  <div class="modal-container">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--amber-50); color: var(--amber-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Sampaikan Aspirasi</h3>
          <div class="modal-subtitle">Sampaikan saran, perbaikan lingkungan, atau kritik langsung ke tabel ASPIRASI</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formKirimAspirasi" action="{{ route('portal.submit-aspirasi') }}" method="POST">
      @csrf
      <input type="hidden" name="nik" id="aspirasiNik" value="{{ $activeCitizen->nik ?? '3201051208940001' }}">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Kategori Masukan / Aduan <span class="req">*</span></label>
          <select class="form-select" id="kategoriAspirasi" name="kategori" required>
            <option value="fasilitas">Fasilitas Umum (Jalan, Lampu, Selokan)</option>
            <option value="keamanan">Keamanan & Ronda Lingkungan</option>
            <option value="kebersihan">Kebersihan & Pengangkutan Sampah</option>
            <option value="kegiatan">Kegiatan Sosial & Keakraban Warga</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Judul Aspirasi / Pokok Bahasan <span class="req">*</span></label>
          <input type="text" class="form-input" id="judulAspirasi" name="judul" placeholder="Misal: Penambahan Tong Sampah di Taman Bermain" required>
        </div>

        <div class="form-group">
          <label class="form-label">Detail Aspirasi / Uraian Masalah <span class="req">*</span></label>
          <textarea class="form-textarea" id="isiAspirasi" name="isi" placeholder="Jelaskan lokasi detail, kronologi, atau usulan solusi Anda..." required style="min-height: 110px;"></textarea>
        </div>

        <div class="form-group">
          <label class="radio-card-label" style="display: flex; align-items: center; justify-content: flex-start; cursor: pointer;">
            <input type="checkbox" id="anonimCheckbox" name="is_anonim" value="1" style="width: 18px; height: 18px;">
            <span class="radio-card-text" style="font-weight: 600; margin-left: 8px;">Kirim Secara Anonim (Kerahasiaan nama terjaga di feed umum)</span>
          </label>
        </div>

        <div id="identitasAspirasiGroup" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Nama Pengirim</label>
            <input type="text" class="form-input" id="namaAspirasi" name="nama" value="{{ $activeCitizen->nama ?? '' }}" placeholder="Nama Lengkap">
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Rumah / Blok</label>
            <input type="text" class="form-input" id="blokAspirasi" name="blok" value="{{ $activeCitizen->alamat ?? '' }}" placeholder="Misal: Blok C-10">
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitAspirasi" style="background-color: var(--amber-600);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
          Kirim Aspirasi Warga
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: USULAN KEGIATAN WARGA
     ========================================================================== -->
<div class="modal-overlay" id="modalUsulanKegiatan" role="dialog" aria-modal="true">
  <div class="modal-container">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Ajukan Usulan Kegiatan Warga</h3>
          <div class="modal-subtitle">Gagas kegiatan positif untuk mempererat tali silaturahmi</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formUsulKegiatan" action="{{ route('portal.usul-kegiatan') }}" method="POST">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Kegiatan yang Diusulkan <span class="req">*</span></label>
          <input type="text" class="form-input" id="namaKegiatanUsul" name="nama_kegiatan" placeholder="Contoh: Turnamen Bulutangkis Antar Gang HUT RI" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Perkiraan Waktu Pelaksanaan <span class="req">*</span></label>
            <input type="text" class="form-input" id="waktuKegiatanUsul" name="waktu" placeholder="Misal: Minggu pagi pertengahan bulan depan" required>
          </div>
          <div class="form-group">
            <label class="form-label">Perkiraan Lokasi <span class="req">*</span></label>
            <input type="text" class="form-input" id="lokasiKegiatanUsul" name="lokasi" placeholder="Balai Warga / Lapangan Blok A" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi & Rencana Partisipan <span class="req">*</span></label>
          <textarea class="form-textarea" id="deskripsiKegiatanUsul" name="deskripsi" placeholder="Jelaskan tujuan kegiatan, siapa saja yang dilibatkan, dan kebutuhan pendukung..." required></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Nama Pengusul <span class="req">*</span></label>
            <input type="text" class="form-input" id="pengusulKegiatan" name="pengusul" placeholder="Nama Warga" required>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor WhatsApp Pengusul <span class="req">*</span></label>
            <input type="tel" class="form-input" id="waPengusul" name="no_wa" placeholder="08xxxxxxxxxx" required>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary">Kirim Usulan ke Pengurus RT</button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: MASUK AKUN WARGA
     ========================================================================== -->
<div class="modal-overlay" id="modalLoginWarga" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 440px;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Masuk Akun Warga</h3>
          <div class="modal-subtitle">Akses riwayat pengajuan surat & bukti iuran</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formLoginWarga">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nomor NIK atau No. WhatsApp Terdaftar</label>
          <input type="text" class="form-input" id="loginIdentifier" placeholder="Contoh: 3201xxxxxxxxxxxx atau 0812345678" required>
        </div>

        <div class="form-group">
          <label class="form-label">PIN Akun Warga (6 Digit)</label>
          <input type="password" class="form-input" id="loginPin" placeholder="******" maxlength="6" required>
          <div class="form-hint">PIN diberikan saat pendataan warga oleh Sekretariat RT.</div>
        </div>

        <div style="font-size: 0.8125rem; color: var(--slate-500); line-height: 1.5;">
          💡 <em>Belum memiliki PIN atau baru pindah ke lingkungan RT 05? Hubungi Sekretaris RT melalui tombol kontak di bagian bawah halaman.</em>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk ke Profil Warga</button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: NOTICE AKSES CMS PENGURUS RT
     ========================================================================== -->
<div class="modal-overlay" id="modalCmsNotice" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 480px;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--slate-100); color: var(--slate-700);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Portal CMS Pengurus RT</h3>
          <div class="modal-subtitle">Khusus Ketua RT, Sekretaris, & Bendahara Terdaftar</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <div class="modal-body" style="text-align: center; padding: 32px 24px;">
      <div style="width: 64px; height: 64px; background-color: var(--primary-50); color: var(--primary-600); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><circle cx="12" cy="11" r="3"/></svg>
      </div>
      <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--slate-900); margin-bottom: 8px;">Area Terbatas Pengurus RT 05</h4>
      <p style="font-size: 0.875rem; color: var(--slate-600); line-height: 1.6; margin-bottom: 20px;">
        Portal CMS digunakan untuk memverifikasi surat pengantar resmi ber-barcode tanda tangan digital, mencatat pembukuan kas masuk/keluar, dan merespons kotak aspirasi warga.
      </p>

      <div style="background-color: var(--slate-50); border: 1px dashed var(--slate-300); border-radius: var(--radius-md); padding: 14px; font-size: 0.8125rem; color: var(--slate-600); text-align: left; margin-bottom: 20px;">
        <strong>Demo Akun Pengurus:</strong><br>
        Gunakan kredensial pengurus RT untuk beralih ke panel dashboard CMS internal.
      </div>

      <a href="{{ route('cms.dashboard') }}" class="btn btn-primary" style="width: 100%; text-decoration: none; justify-content: center;">
        Buka Panel Admin CMS (Masuk Dashboard)
      </a>
    </div>
  </div>
</div>

<!-- ==========================================================================
     MODAL: CEK STATUS SURAT & MUTASI IURAN MANDIRI WARGA
     ========================================================================== -->
<div class="modal-overlay" id="modalStatusWarga" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 780px;">
    <!-- Modal Header -->
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" x2="8" y1="13" y2="13"/>
            <line x1="16" x2="8" y1="17" y2="17"/>
            <line x1="10" x2="8" y1="9" y2="9"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Layanan Riwayat & Status Warga</h3>
          <div class="modal-subtitle">Pantau status ACC surat pengantar RT & riwayat mutasi iuran bulanan keluarga</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body" style="padding: 20px 24px;">
      <!-- Quick Search & Citizen Switcher Filter Bar -->
      <div style="background-color: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 14px 16px; margin-bottom: 20px;">
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <div style="font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.5px;">
            Cari Akun Warga atau No. Rumah Lain:
          </div>
          <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
              <input 
                type="text" 
                id="searchWargaQueryInput" 
                class="form-input" 
                placeholder="Ketik NIK 16 digit, No. Blok (misal: B-12), atau nama..." 
                style="padding: 8px 14px; font-size: 0.8125rem; background-color: #ffffff;"
              >
            </div>
            <button type="button" class="btn btn-primary" id="btnSearchWargaRiwayat" style="padding: 8px 18px; font-size: 0.8125rem;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              <span>Cari Data</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Active Citizen Profile Card -->
      <div style="background-color: #ffffff; border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 16px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px; box-shadow: var(--shadow-xs);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div class="citizen-avatar" id="modalCitizenAvatar" style="width: 44px; height: 44px; font-size: 1rem;">
              {{ strtoupper(substr($activeCitizen->nama ?? 'HW', 0, 2)) }}
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 6px;">
                <h4 style="font-size: 1rem; font-weight: 800; color: var(--slate-900); line-height: 1.2;" id="modalCitizenName">
                  {{ $activeCitizen->nama ?? 'Bpk. Hendra Wijaya' }}
                </h4>
                <span class="badge badge-blue" style="font-size: 0.6875rem;" id="modalCitizenDomisili">
                  {{ ucfirst($activeCitizen->status_domisili ?? 'Tetap') }}
                </span>
              </div>
              <p style="font-size: 0.75rem; color: var(--slate-500); margin-top: 2px;" id="modalCitizenAddress">
                {{ $activeCitizen->alamat ?? 'Blok B No. 12' }} • No. KK: {{ $activeCitizen->no_kk ?? '3201051909120008' }} • NIK: {{ $activeCitizen->nik ?? '3201051208940001' }}
              </p>
            </div>
          </div>

          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="badge badge-emerald" id="modalSummarySuratBadge" style="font-size: 0.75rem; padding: 4px 10px;">
              {{ $citizenRiwayat['summary']['total_surat_acc'] ?? 0 }} Surat Di-ACC
            </span>
            <span class="badge {{ ($citizenRiwayat['summary']['is_lunas_bulan_ini'] ?? false) ? 'badge-emerald' : 'badge-amber' }}" id="modalSummaryIuranBadge" style="font-size: 0.75rem; padding: 4px 10px;">
              {{ ($citizenRiwayat['summary']['is_lunas_bulan_ini'] ?? false) ? 'Iuran Sep: Lunas' : 'Iuran Sep: Belum Lunas' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="warga-tab-nav">
        <button type="button" class="warga-tab-btn active" data-tab-target="tabSuratWarga" id="btnTabSurat">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" x2="8" y1="13" y2="13"/>
            <line x1="16" x2="8" y1="17" y2="17"/>
          </svg>
          <span>Status Pengajuan Surat (Cek ACC)</span>
          <span class="badge badge-emerald" id="tabSuratCountBadge" style="font-size: 0.6875rem;">
            {{ count($citizenRiwayat['letters'] ?? []) }}
          </span>
        </button>

        <button type="button" class="warga-tab-btn" data-tab-target="tabMutasiWarga" id="btnTabMutasi">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" x2="12" y1="2" y2="22"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
          <span>Mutasi Iuran Kas Keluarga</span>
          <span class="badge badge-blue" id="tabMutasiCountBadge" style="font-size: 0.6875rem;">
            {{ count($citizenRiwayat['mutations'] ?? []) }}
          </span>
        </button>
      </div>

      <!-- ====================================================================
           TAB CONTENT 1: STATUS SURAT PENGANTAR (ACC / PENDING / REJECTED)
           ==================================================================== -->
      <div class="warga-tab-content active" id="tabSuratWarga">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
          <div>
            <h4 style="font-size: 0.875rem; font-weight: 800; color: var(--slate-900);">Daftar Surat Pengantar & Resi Permohonan</h4>
            <p style="font-size: 0.75rem; color: var(--slate-500);">Dokumen yang sudah di-ACC Ketua RT dapat langsung diunduh salinan PDF aslinya.</p>
          </div>
          <button type="button" class="btn btn-primary" data-modal="modalSurat" style="font-size: 0.75rem; padding: 6px 12px;">
            + Ajukan Surat Baru
          </button>
        </div>

        <div class="surat-card-list" id="suratCardsListWrapper">
          @forelse($citizenRiwayat['letters'] ?? [] as $let)
            <div class="surat-card-item {{ $let['is_acc'] ? 'acc-approved' : ($let['is_rejected'] ? 'acc-rejected' : 'acc-pending') }}">
              <div class="surat-header-row">
                <div>
                  <div class="surat-title-bold">{{ $let['jenis_surat'] }}</div>
                  <div class="surat-number-tag">
                    No: {{ $let['nomor_surat'] }}
                  </div>
                </div>

                <div>
                  @if($let['is_acc'])
                    <span class="badge badge-emerald" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                      <span>SUDAH DI-ACC KETUA RT</span>
                    </span>
                  @elseif($let['is_rejected'])
                    <span class="badge badge-rose" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px;">
                      DITOLAK / REVISI
                    </span>
                  @else
                    <span class="badge badge-amber" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                      <span style="width: 6px; height: 6px; border-radius: 50%; background-color: var(--amber-500); animation: pulse 1.5s infinite;"></span>
                      <span>MENUNGGU PERSETUJUAN</span>
                    </span>
                  @endif
                </div>
              </div>

              <div class="surat-desc-text">
                <strong>Keperluan:</strong> {{ $let['keperluan'] }}
              </div>

              <div style="font-size: 0.75rem; color: var(--slate-500); background-color: var(--slate-50); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px dashed var(--slate-200);">
                💡 <em>{{ $let['keterangan_acc'] }}</em>
              </div>

              <div class="surat-action-footer">
                <div style="font-size: 0.75rem; color: var(--slate-400);">
                  Diajukan: <strong>{{ $let['tanggal'] }}</strong>
                </div>

                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  @if($let['is_acc'])
                    <a href="{{ $let['pdf_url'] }}" target="_blank" class="btn btn-primary" style="font-size: 0.75rem; padding: 6px 12px; gap: 6px; text-decoration: none;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      <span>Unduh PDF Ber-QR Code</span>
                    </a>
                    <a href="{{ $let['verify_url'] }}" target="_blank" class="btn btn-subtle" style="font-size: 0.75rem; padding: 6px 12px; text-decoration: none;">
                      Verifikasi Digital
                    </a>
                  @else
                    <span style="font-size: 0.75rem; color: var(--amber-700); font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 14 14"/>
                      </svg>
                      <span>Menunggu Tanda Tangan Pak RT</span>
                    </span>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div style="text-align: center; padding: 36px 16px; background-color: var(--slate-50); border: 1px dashed var(--slate-300); border-radius: var(--radius-lg); color: var(--slate-500); font-size: 0.8125rem;">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--slate-400); margin: 0 auto 8px auto;">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
              </svg>
              <div>Belum ada arsip permohonan surat untuk akun ini.</div>
              <button type="button" class="btn btn-primary" data-modal="modalSurat" style="margin-top: 12px; font-size: 0.75rem;">
                + Buat Permohonan Surat Sekarang
              </button>
            </div>
          @endforelse
        </div>
      </div>

      <!-- ====================================================================
           TAB CONTENT 2: BUKU MUTASI IURAN MANDIRI
           ==================================================================== -->
      <div class="warga-tab-content" id="tabMutasiWarga">
        <!-- Summary Cards for Household Dues -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 18px;">
          <div style="background-color: var(--emerald-50); border: 1px solid var(--emerald-100); border-radius: var(--radius-md); padding: 14px 16px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--emerald-800); text-transform: uppercase;">Tagihan September 2026:</span>
            <div style="font-size: 1.125rem; font-weight: 800; color: var(--emerald-700); margin-top: 4px;" id="modalIuranStatusText">
              {{ ($citizenRiwayat['summary']['is_lunas_bulan_ini'] ?? false) ? 'LUNAS (Rp 75.000)' : 'BELUM DIBAYAR' }}
            </div>
            <span style="font-size: 0.6875rem; color: var(--emerald-600);">Keamanan & Sampah Lingkungan RT 05</span>
          </div>

          <div style="background-color: var(--primary-50); border: 1px solid var(--primary-100); border-radius: var(--radius-md); padding: 14px 16px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary-800); text-transform: uppercase;">Total Iuran Terbayar 2026:</span>
            <div style="font-size: 1.125rem; font-weight: 800; color: var(--primary-700); margin-top: 4px;" id="modalTotalIuranText">
              {{ $citizenRiwayat['summary']['total_terbayar_tahun_ini'] ?? 'Rp 0' }}
            </div>
            <span style="font-size: 0.6875rem; color: var(--primary-600);">Tercatat sah di pembukuan kas bendahara</span>
          </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
          <div>
            <h4 style="font-size: 0.875rem; font-weight: 800; color: var(--slate-900);">Riwayat Mutasi Pembayaran Iuran Keluarga</h4>
            <p style="font-size: 0.75rem; color: var(--slate-500);">Daftar setoran kas bulanan terverifikasi via QRIS dan transfer perbankan.</p>
          </div>
          <button type="button" class="btn btn-success" data-modal="modalIuran" style="font-size: 0.75rem; padding: 6px 12px;">
            + Bayar Iuran / Konfirmasi
          </button>
        </div>

        <!-- Table Container -->
        <div style="overflow-x: auto; border: 1px solid var(--slate-200); border-radius: var(--radius-lg); background-color: #ffffff;">
          <table class="mutasi-table">
            <thead>
              <tr>
                <th>Periode</th>
                <th>Tanggal</th>
                <th>Nominal</th>
                <th>Kategori & Metode</th>
                <th>Status Verifikasi</th>
                <th style="text-align: right;">Bukti</th>
              </tr>
            </thead>
            <tbody id="mutasiTableBody">
              @forelse($citizenRiwayat['mutations'] ?? [] as $mut)
                <tr>
                  <td>
                    <strong style="color: var(--slate-900); font-size: 0.8125rem;">{{ $mut['periode'] }}</strong>
                    <div style="font-size: 0.6875rem; color: var(--slate-400); font-family: monospace;">{{ $mut['ref_code'] }}</div>
                  </td>
                  <td style="color: var(--slate-600); white-space: nowrap;">
                    {{ $mut['tanggal'] }}
                  </td>
                  <td>
                    <strong style="color: var(--emerald-600); font-weight: 800;">{{ $mut['nominal_formatted'] }}</strong>
                  </td>
                  <td>
                    <div style="font-weight: 600; color: var(--slate-800);">{{ $mut['kategori'] }}</div>
                    <div style="font-size: 0.6875rem; color: var(--slate-500);">{{ $mut['metode'] }}</div>
                  </td>
                  <td>
                    @if($mut['is_lunas'])
                      <span class="badge badge-emerald" style="font-size: 0.6875rem;">✓ Lunas Sah</span>
                    @else
                      <span class="badge badge-amber" style="font-size: 0.6875rem;">⏳ Menunggu Bendahara</span>
                    @endif
                  </td>
                  <td style="text-align: right;">
                    <button type="button" class="btn btn-subtle btn-view-receipt" data-ref="{{ $mut['ref_code'] }}" data-periode="{{ $mut['periode'] }}" data-nominal="{{ $mut['nominal_formatted'] }}" data-tanggal="{{ $mut['tanggal'] }}" style="padding: 4px 8px; font-size: 0.6875rem;">
                      Struk Sah
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align: center; padding: 24px; color: var(--slate-400);">
                    Belum ada catatan mutasi iuran untuk unit rumah ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer" style="background-color: var(--slate-50); border-top: 1px solid var(--slate-200); padding: 14px 24px; display: flex; justify-content: space-between; align-items: center;">
      <span style="font-size: 0.75rem; color: var(--slate-500);">
        Data kependudukan & mutasi terhubung ke <strong>Database RT 05 / RW 01 Harmoni Residence</strong>
      </span>
      <button type="button" class="btn btn-subtle" data-close-modal>Tutup Jendela</button>
    </div>
  </div>
</div>

<!-- ==========================================================================
     MODAL: EDIT PROFIL WARGA MANDIRI
     ========================================================================== -->
<div class="modal-overlay" id="modalEditProfil" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 520px;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Edit Profil Warga</h3>
          <div class="modal-subtitle">Perbarui data kontak dan kata sandi akun portal mandiri Anda</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formEditProfil" action="{{ route('portal.edit-profil') }}" method="POST">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nomor Induk Kependudukan (NIK)</label>
          <input type="text" class="form-input" value="{{ $warga->NIK ?? '' }}" readonly style="background-color: #F1F5F9; color: #64748B; cursor: not-allowed;">
          <div class="form-hint">NIK adalah identitas kependudukan permanen yang terikat pada data RT.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor Kartu Keluarga (KK)</label>
          <input type="text" class="form-input" value="{{ $warga->ID_KK ?? '' }}" readonly style="background-color: #F1F5F9; color: #64748B; cursor: not-allowed;">
        </div>

        <div class="form-group">
          <label class="form-label">Nama Lengkap Sesuai KTP <span class="req">*</span></label>
          <input type="text" class="form-input" id="editNamaLengkap" name="nama_lengkap" value="{{ $warga->Nama_Lengkap ?? '' }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor Telepon / WhatsApp <span class="req">*</span></label>
          <input type="tel" class="form-input" id="editNoTelp" name="no_telp" value="{{ $warga->no_telp ?? '' }}" placeholder="08xxxxxxxxxx" required>
        </div>

        <div style="border-top: 1px solid #E2E8F0; padding-top: 14px; margin-top: 14px;">
          <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">Ubah Kata Sandi (Opsional)</div>
          <div class="form-group">
            <label class="form-label">Kata Sandi Baru</label>
            <input type="password" class="form-input" id="editPassword" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
          </div>
          <div class="form-group">
            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
            <input type="password" class="form-input" id="editPasswordConfirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" minlength="6">
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitEditProfil">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>
