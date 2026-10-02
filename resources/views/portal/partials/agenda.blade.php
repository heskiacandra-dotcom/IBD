<section class="section agenda-section" id="kegiatan">
  <div class="container">
    <div class="agenda-header-row">
      <div>
        <span class="section-badge">Agenda & Guyub Warga</span>
        <h2 class="section-title">Kegiatan Mendatang Lingkungan</h2>
        <p class="section-description">
          Jangan lewatkan agenda gotong royong, posyandu, dan pertemuan silaturahmi RT bulan ini.
        </p>
      </div>
      <button class="btn btn-outline-primary" data-modal="modalUsulanKegiatan">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="16"/>
          <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
        Ajukan Usulan Kegiatan Warga
      </button>
    </div>

    <div class="agenda-grid">
      @foreach($kegiatan as $event)
        <article class="event-card">
          <div class="event-top">
            <div class="event-date-box">
              <div class="event-day">{{ $event['day'] }}</div>
              <div class="event-month">{{ $event['month'] }}</div>
            </div>
            <span class="badge {{ $event['badge_class'] }}">{{ $event['badge'] }}</span>
          </div>
          <h3 class="event-title">{{ $event['title'] }}</h3>
          <div class="event-meta">
            <div class="event-meta-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
              <span>{{ $event['time'] }}</span>
            </div>
            <div class="event-meta-item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
              <span>{{ $event['location'] }}</span>
            </div>
          </div>
          <div class="event-footer">
            <div class="event-organizer">Penyelenggara: <strong>{{ $event['organizer'] }}</strong></div>
            <button class="btn btn-subtle btn-sm btn-action-detail" data-event="{{ $event['title'] }}">Rincian</button>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
