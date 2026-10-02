<header class="navbar" id="topNavbar">
  <div class="container nav-container">
    <!-- Brand Logo -->
    <a href="{{ route('portal.home') }}" class="brand-logo" id="brandLogo" aria-label="RuangWarga RT 05">
      <div class="brand-icon">
        <!-- Geometric House / Community SVG -->
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
      <div class="brand-text-wrap">
        <span class="brand-name">Smart<span class="brand-highlight">RT</span></span>
        <span class="brand-sub">RuangWarga Digital</span>
      </div>
    </a>

    <!-- Navigation Bar: Beranda, Layanan Mandiri, Agenda Lingkungan, Transparansi Kas, Aspirasi -->
    <nav class="nav-menu" id="navMenu">
      <div class="nav-links-wrapper" id="navLinks">
        <!-- Beranda -->
        <a href="{{ route('portal.home') }}" class="nav-text-link {{ request()->routeIs('portal.home') ? 'active' : '' }}" id="navLinkBeranda">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
          <span>Beranda</span>
        </a>

        <!-- Layanan Mandiri -->
        <a href="{{ route('portal.layanan') }}" class="nav-text-link {{ request()->routeIs('portal.layanan') ? 'active' : '' }}" id="navLinkLayanan">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" x2="8" y1="13" y2="13"/>
            <line x1="16" x2="8" y1="17" y2="17"/>
          </svg>
          <span>Layanan Mandiri</span>
        </a>

        <!-- Agenda Lingkungan -->
        <a href="{{ route('portal.agenda') }}" class="nav-text-link {{ request()->routeIs('portal.agenda') ? 'active' : '' }}" id="navLinkAgenda">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
            <line x1="16" x2="16" y1="2" y2="6"/>
            <line x1="8" x2="8" y1="2" y2="6"/>
            <line x1="3" x2="21" y1="10" y2="10"/>
          </svg>
          <span>Agenda Lingkungan</span>
        </a>

        <!-- Transparansi Kas -->
        <a href="{{ route('portal.transparansi') }}" class="nav-text-link {{ request()->routeIs('portal.transparansi') ? 'active' : '' }}" id="navLinkTransparansi">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" x2="12" y1="2" y2="22"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
          <span>Transparansi Kas</span>
        </a>

        <!-- Aspirasi -->
        <a href="{{ route('portal.aspirasi') }}" class="nav-text-link {{ request()->routeIs('portal.aspirasi') ? 'active' : '' }}" id="navLinkAspirasi">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          <span>Aspirasi</span>
        </a>
      </div>
    </nav>

    <!-- Nav Actions: Notification Bell, Citizen Status, & Login CTA -->
    <div class="nav-actions">
      <!-- Notification / Bell Icon with Dropdown -->
      <div class="nav-notif-wrapper">
        <button class="nav-action-icon-btn btn-notif-icon" id="btnToggleNotif" data-tooltip="Pengumuman RT" aria-label="Pengumuman Lingkungan RT">
          <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
          <span class="notif-badge-dot"></span>
        </button>

        <!-- Notification Popover Card -->
        <div class="notif-dropdown" id="notifDropdown" role="dialog" aria-label="Notifikasi Pengumuman">
          <div class="notif-dropdown-header">
            <div class="notif-header-title">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary-600);">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
              </svg>
              <span>Pengumuman RT 05</span>
            </div>
            <span class="badge badge-amber" style="font-size: 0.6875rem;">1 Baru</span>
          </div>

          <div class="notif-dropdown-body">
            <div class="notif-item">
              <div class="notif-item-top">
                <span class="notif-tag">Mendesak</span>
                <span class="notif-time">26 Sep 2026 • 07.00 WIB</span>
              </div>
              <h4 class="notif-item-title">{{ $ticker['title'] ?? 'Kerja Bakti & Fogging Nyamuk DBD' }}</h4>
              <p class="notif-item-desc">{{ $ticker['content'] ?? 'Pelaksanaan Sabtu, 26 September 2026 pukul 07.00 WIB. Mohon warga menutup penampungan air dan mendampingi petugas.' }}</p>
              <a href="{{ $ticker['link'] ?? '#kegiatan' }}" class="notif-item-link" id="notifActionLink">
                <span>Rincian Kegiatan Lengkap</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="9 18 15 12 9 6"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Cek Status Surat & Mutasi Iuran Saya -->
      <button type="button" class="nav-action-icon-btn" data-modal="modalStatusWarga" id="btnNavbarStatusWarga" data-tooltip="Cek Status Surat & Mutasi Iuran Saya ({{ $activeCitizen->nama ?? 'Warga' }})" aria-label="Status Surat dan Mutasi Warga">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" x2="8" y1="13" y2="13"/>
          <line x1="16" x2="8" y1="17" y2="17"/>
        </svg>
        <span class="notif-badge-dot" style="background-color: var(--emerald-500);"></span>
      </button>

      @if(auth('warga')->check())
        <div style="display: flex; align-items: center; gap: 8px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem;">
            {{ strtoupper(substr(auth('warga')->user()->Nama_Lengkap, 0, 2)) }}
          </div>
          <span style="font-size: 0.8125rem; font-weight: 600; color: #0F172A; display: none; @media(min-width: 640px){display:inline;}">
            {{ Str::limit(auth('warga')->user()->Nama_Lengkap, 14) }}
          </span>
          <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
            @csrf
            <button type="submit" class="btn btn-subtle btn-sm" title="Keluar" style="font-size: 0.75rem; padding: 4px 8px; color: #DC2626; border: 1px solid #FEE2E2; background: #FEF2F2; border-radius: 6px;">
              Keluar
            </button>
          </form>
        </div>
      @elseif(auth('web')->check())
        <div style="display: flex; align-items: center; gap: 8px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem;">
            {{ strtoupper(substr(auth('web')->user()->Username, 0, 2)) }}
          </div>
          <span style="font-size: 0.8125rem; font-weight: 600; color: #0F172A; display: none; @media(min-width: 640px){display:inline;}">
            {{ Str::limit(auth('web')->user()->Username, 14) }}
          </span>
          <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
            @csrf
            <button type="submit" class="btn btn-subtle btn-sm" title="Keluar" style="font-size: 0.75rem; padding: 4px 8px; color: #DC2626; border: 1px solid #FEE2E2; background: #FEF2F2; border-radius: 6px;">
              Keluar
            </button>
          </form>
        </div>
      @else
        <!-- Prominent Login CTA Button -->
        <a href="{{ route('login') }}" class="btn btn-primary btn-sm login-cta-btn" id="btnNavbarLoginCta" aria-label="Masuk ke Sistem SSO RuangWarga">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
            <polyline points="10 17 15 12 10 7"/>
            <line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          <span>Login CTA</span>
        </a>
      @endif
    </div>

    <!-- Mobile Hamburger Toggle -->
    <button class="mobile-toggle" id="mobileMenuBtn" aria-label="Buka Menu Navigasi">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" x2="20" y1="12" y2="12"/>
        <line x1="4" x2="20" y1="6" y2="6"/>
        <line x1="4" x2="20" y1="18" y2="18"/>
      </svg>
    </button>
  </div>
</header>
