<aside class="alert-ticker-bar" aria-label="Pengumuman Lingkungan">
  <div class="container ticker-inner">
    <div style="display: flex; align-items: center; gap: 10px; overflow: hidden;">
      <span class="ticker-label">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
          <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        {{ $ticker['label'] ?? 'Pengumuman RT' }}
      </span>
      <div class="ticker-content">
        <strong>{{ $ticker['title'] ?? '' }}:</strong> {{ $ticker['content'] ?? '' }}
      </div>
    </div>
    <a href="{{ $ticker['link'] ?? '#kegiatan' }}" class="ticker-action">
      Rincian Jadwal
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="9 18 15 12 9 6"/>
      </svg>
    </a>
  </div>
</aside>
