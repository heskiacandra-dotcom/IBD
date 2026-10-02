<!-- Mobile Bottom App Bar (Native App-Like Experience) -->
<nav class="mobile-bottom-nav" id="mobileBottomNav" aria-label="Navigasi Bawah Ponsel">
  <div class="mobile-bottom-nav-inner">
    
    <!-- 1. Beranda -->
    <a href="{{ route('portal.home') }}" class="mobile-nav-item {{ request()->routeIs('portal.home') ? 'active' : '' }}" id="bottomNavBeranda" aria-label="Beranda">
      <div class="mobile-nav-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('portal.home') ? '2.4' : '2' }}" stroke-linecap="round" stroke-linejoin="round">
          <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
      <span class="mobile-nav-label">Beranda</span>
    </a>

    <!-- 2. Layanan -->
    <a href="{{ route('portal.layanan') }}" class="mobile-nav-item {{ request()->routeIs('portal.layanan') ? 'active' : '' }}" id="bottomNavLayanan" aria-label="Layanan Mandiri">
      <div class="mobile-nav-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('portal.layanan') ? '2.4' : '2' }}" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
          <polyline points="14 2 14 8 20 8"/>
          <line x1="16" x2="8" y1="13" y2="13"/>
          <line x1="16" x2="8" y1="17" y2="17"/>
        </svg>
      </div>
      <span class="mobile-nav-label">Layanan</span>
    </a>

    <!-- 3. Agenda -->
    <a href="{{ route('portal.agenda') }}" class="mobile-nav-item {{ request()->routeIs('portal.agenda') ? 'active' : '' }}" id="bottomNavAgenda" aria-label="Agenda Kegiatan">
      <div class="mobile-nav-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('portal.agenda') ? '2.4' : '2' }}" stroke-linecap="round" stroke-linejoin="round">
          <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
          <line x1="16" x2="16" y1="2" y2="6"/>
          <line x1="8" x2="8" y1="2" y2="6"/>
          <line x1="3" x2="21" y1="10" y2="10"/>
        </svg>
      </div>
      <span class="mobile-nav-label">Agenda</span>
    </a>

    <!-- 4. Kas RT -->
    <a href="{{ route('portal.transparansi') }}" class="mobile-nav-item {{ request()->routeIs('portal.transparansi') ? 'active' : '' }}" id="bottomNavKas" aria-label="Transparansi Kas">
      <div class="mobile-nav-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('portal.transparansi') ? '2.4' : '2' }}" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" x2="12" y1="2" y2="22"/>
          <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
        </svg>
      </div>
      <span class="mobile-nav-label">Kas RT</span>
    </a>

    <!-- 5. Aspirasi -->
    <a href="{{ route('portal.aspirasi') }}" class="mobile-nav-item {{ request()->routeIs('portal.aspirasi') ? 'active' : '' }}" id="bottomNavAspirasi" aria-label="Aspirasi Warga">
      <div class="mobile-nav-icon-wrap">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ request()->routeIs('portal.aspirasi') ? '2.4' : '2' }}" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
      </div>
      <span class="mobile-nav-label">Aspirasi</span>
    </a>

  </div>
</nav>
