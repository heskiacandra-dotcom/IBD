<section class="section hero-section" id="beranda">
  <div class="container">
    <!-- Hero Text Content -->
    <div class="hero-content">
      <div class="hero-pill">
        <span class="hero-pill-dot"></span>
        <span>Portal Pelayanan Mandiri Resmi Warga RT 05 / RW 01 • Harmoni Residence</span>
      </div>

      <h1 class="hero-title">
        Satu Wadah Digital untuk <br>
        <span class="highlight">Kemudahan Warga</span> & Kebaikan Bersama
      </h1>

      <p class="hero-subtitle">
        Ajukan surat pengantar, bayar iuran bulanan, pinjam perlengkapan warga, dan pantau kegiatan lingkungan secara transparan tanpa perlu antre di rumah pengurus.
      </p>

      <div class="hero-trust-chips">
        <div class="trust-chip">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <span>100% Bebas Antre & Mandiri</span>
        </div>
        <div class="trust-chip">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
          <span>Transparan & Akuntabel</span>
        </div>
        <div class="trust-chip">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 14 14"/>
          </svg>
          <span>Respon Cepat Pengurus RT</span>
        </div>
      </div>
    </div>

    <!-- ====================================================================
         CITIZEN STATUS & MUTASI QUICK ACCESS BANNER (PORTAL WARGA MANDIRI)
         ==================================================================== -->
    <div class="citizen-hub-banner" id="citizenHubBanner">
      <div class="citizen-hub-top">
        <div class="citizen-profile-info">
          <div class="citizen-avatar">
            {{ strtoupper(substr($activeCitizen->nama ?? 'HW', 0, 2)) }}
          </div>
          <div class="citizen-name-wrap">
            <div style="display: flex; align-items: center; gap: 8px;">
              <span class="badge badge-blue" style="font-size: 0.6875rem;">Akun Warga Aktif</span>
              <span style="font-size: 0.75rem; color: var(--slate-400);">• RT 05 / RW 01</span>
            </div>
            <h3 id="bannerCitizenName">{{ $activeCitizen->nama ?? 'Bpk. Hendra Wijaya' }}</h3>
            <p id="bannerCitizenMeta">
              <span id="bannerCitizenAlamat">{{ $activeCitizen->alamat ?? 'Blok B No. 12' }}</span> • NIK: <code style="font-size: 0.75rem;">{{ $activeCitizen->nik ?? '3201051208940001' }}</code>
            </p>
          </div>
        </div>

        <div class="citizen-badge-strip">
          <!-- Status Surat ACC Chip -->
          <a href="#" class="citizen-stat-chip {{ ($citizenRiwayat['summary']['total_surat_acc'] ?? 0) > 0 ? 'chip-surat-acc' : 'chip-surat-pending' }}" data-open-warga-tab="surat">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            <span id="bannerSuratAccText">
              @if(($citizenRiwayat['summary']['total_surat_acc'] ?? 0) > 0)
                {{ $citizenRiwayat['summary']['total_surat_acc'] }} Surat Sudah Di-ACC RT
              @else
                Belum Ada Surat Terbit
              @endif
            </span>
          </a>

          <!-- Status Iuran Chip -->
          <a href="#" class="citizen-stat-chip {{ ($citizenRiwayat['summary']['is_lunas_bulan_ini'] ?? false) ? 'chip-iuran-lunas' : 'chip-iuran-unpaid' }}" data-open-warga-tab="mutasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <rect width="20" height="14" x="2" y="5" rx="2"/>
              <line x1="2" x2="22" y1="10" y2="10"/>
            </svg>
            <span id="bannerIuranText">
              {{ ($citizenRiwayat['summary']['is_lunas_bulan_ini'] ?? false) ? 'Iuran Bulan Ini: Lunas (Rp 75.000)' : 'Iuran Bulan Ini: Belum Lunas' }}
            </span>
          </a>
        </div>
      </div>

      <div class="citizen-hub-bottom">
        <div class="citizen-actions-row">
          <button type="button" class="btn btn-primary" data-open-warga-tab="surat" style="font-size: 0.8125rem; padding: 8px 16px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
            <span>Cek Status Surat Saya</span>
          </button>

          <button type="button" class="btn btn-success" data-open-warga-tab="mutasi" style="font-size: 0.8125rem; padding: 8px 16px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" x2="12" y1="2" y2="22"/>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            <span>Mutasi Iuran Saya</span>
          </button>
        </div>

        <!-- Quick Switch Demo Citizen Dropdown -->
        <div style="display: flex; align-items: center; gap: 8px;">
          <span style="font-size: 0.75rem; color: var(--slate-500); font-weight: 600;">Ganti Akun Warga:</span>
          <select id="quickSwitchCitizenSelect" class="form-select" style="font-size: 0.75rem; padding: 6px 12px; width: auto; background-color: var(--slate-50);">
            @foreach($allCitizens as $c)
              <option value="{{ $c->nik }}" {{ ($activeCitizen->nik ?? '') === $c->nik ? 'selected' : '' }}>
                {{ $c->nama }} ({{ $c->alamat }})
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </div>

    <!-- 4 Quick Action Feature Cards -->
    <div class="quick-action-grid" id="layanan">
      <!-- Card 1: Pengajuan Surat -->
      <div class="action-card">
        <div class="action-icon-wrapper action-icon-blue">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" x2="8" y1="13" y2="13"/>
            <line x1="16" x2="8" y1="17" y2="17"/>
            <line x1="10" x2="8" y1="9" y2="9"/>
          </svg>
        </div>
        <h2 class="action-title">Surat Pengantar Online</h2>
        <p class="action-desc">
          Surat pengantar Domisili, pembuatan KTP/KK, SKCK, hingga keterangan usaha diproses resmi via prosedur RT.
        </p>
        <button class="btn btn-primary action-cta" data-modal="modalSurat">
          <span>Buat Surat</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </div>

      <!-- Card 2: Bayar Iuran Bulanan -->
      <div class="action-card card-green">
        <div class="action-icon-wrapper action-icon-green">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="20" height="14" x="2" y="5" rx="2"/>
            <line x1="2" x2="22" y1="10" y2="10"/>
          </svg>
        </div>
        <h2 class="action-title">Bayar Iuran Bulanan</h2>
        <p class="action-desc">
          Cek tagihan kas kebersihan & keamanan warga secara transparan. Pembayaran QRIS & upload bukti transfer langsung terverifikasi.
        </p>
        <button class="btn btn-success action-cta" data-modal="modalIuran">
          <span>Bayar Iuran</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </div>

      <!-- Card 3: Pinjam Inventaris RT -->
      <div class="action-card card-purple">
        <div class="action-icon-wrapper action-icon-purple">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
            <line x1="12" x2="12" y1="22.08" y2="12"/>
          </svg>
        </div>
        <h2 class="action-title">Pinjam Inventaris RT</h2>
        <p class="action-desc">
          Katalog lengkap inventaris balai RT dengan status ketersediaan stok: tenda lipat, kursi, sound system, dan proyektor.
        </p>
        <button class="btn btn-primary action-cta" style="background-color: var(--purple-600);" data-modal="modalPinjam">
          <span>Pinjam Inventaris</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </div>

      <!-- Card 4: Sampaikan Aspirasi -->
      <div class="action-card card-amber">
        <div class="action-icon-wrapper action-icon-amber">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
        </div>
        <h2 class="action-title">Sampaikan Aspirasi</h2>
        <p class="action-desc">
          Kotak saran, laporan penerangan jalan mati, atau ide perbaikan lingkungan. Dapat dikirim anonim dan ditanggapi terbuka.
        </p>
        <button class="btn btn-primary action-cta" style="background-color: var(--amber-600);" data-modal="modalAspirasi">
          <span>Kirim Aspirasi</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </div>
    </div>
  </div>
</section>
