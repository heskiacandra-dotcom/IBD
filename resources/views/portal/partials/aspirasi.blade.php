<section class="section aspirasi-section" id="aspirasi">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Demokrasi & Keterbukaan Publik</span>
      <h2 class="section-title">Suara Warga & Tanggapan Pengurus RT</h2>
      <p class="section-description">
        Kotak aspirasi, aduan fasilitas lingkungan, dan masukan konstruktif warga yang ditanggapi secara terbuka dan transparan oleh Pengurus RT.
      </p>
    </div>

    <!-- Controls: Filter & Write Aspirasi Button -->
    <div class="aspirasi-controls">
      <div class="filter-pills" id="aspirasiFilters">
        <button class="filter-btn active" data-filter="all">Semua Aspirasi ({{ count($aspirasi) }})</button>
        <button class="filter-btn" data-filter="fasilitas">Fasilitas Umum</button>
        <button class="filter-btn" data-filter="keamanan">Keamanan & Ronda</button>
        <button class="filter-btn" data-filter="kebersihan">Kebersihan</button>
      </div>

      <button class="btn btn-primary" data-modal="modalAspirasi" id="btnBukaModalAspirasi">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
          <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
        </svg>
        <span>Sampaikan Aspirasi</span>
      </button>
    </div>

    <!-- Public Aspirasi Feed: Displays verified citizen feedback alongside responses from RESPON_ASPIRASI -->
    <div class="aspirasi-feed" id="aspirasiFeedContainer">
      @forelse($aspirasi as $item)
        <article class="aspirasi-card" data-category="{{ $item['category'] }}">
          <div class="aspirasi-header">
            <div class="aspirasi-user-info">
              <div class="user-avatar-circle" style="background-color: {{ $item['user_bg'] }}; color: {{ $item['user_color'] }}; font-weight: 700;">
                {{ $item['user_initial'] }}
              </div>
              <div class="user-name-wrapper">
                <div class="user-display-name">{{ $item['user_name'] }}</div>
                <div class="aspirasi-timestamp">{{ $item['time'] }} • Kategori: {{ $item['category_label'] }}</div>
              </div>
            </div>
            <span class="badge {{ $item['status_class'] }}">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
              {{ $item['status_label'] }}
            </span>
          </div>

          <h4 class="aspirasi-title">{{ $item['title'] }}</h4>
          <p class="aspirasi-body">{{ $item['content'] }}</p>

          <!-- Tanggapan Resmi Pengurus RT (RESPON_ASPIRASI) -->
          @if(isset($item['response']) && !empty($item['response']['content']))
            <div class="official-response-box">
              <div class="response-header">
                <div class="responder-title">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                  <span>Tanggapan Resmi: {{ $item['response']['responder'] }}</span>
                </div>
                <div class="responder-time">Direspons {{ $item['response']['time'] }}</div>
              </div>
              <p class="response-content">
                {{ $item['response']['content'] }}
              </p>
            </div>
          @else
            <div style="background-color: var(--slate-50); border: 1px dashed var(--slate-200); border-radius: var(--radius-sm); padding: 10px 14px; font-size: 0.75rem; color: var(--slate-500); margin-top: 14px; display: flex; align-items: center; gap: 8px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 14 14"/></svg>
              <span>Aspirasi telah masuk agenda telaah pengurus RT. Tanggapan resmi akan segera dipublikasikan di feed ini.</span>
            </div>
          @endif
        </article>
      @empty
        <div style="padding: 40px 20px; text-align: center; background-color: #FFFFFF; border: 1px dashed var(--slate-300); border-radius: var(--radius-lg); color: var(--slate-500); grid-column: 1 / -1;">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px auto; color: var(--slate-400);">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          <div style="font-size: 0.9375rem; font-weight: 700; color: var(--slate-800);">Belum Ada Aspirasi Publik</div>
          <p style="font-size: 0.8125rem; margin-top: 4px;">Jadilah warga pertama yang menyampaikan aspirasi atau ide perbaikan untuk lingkungan RT 05.</p>
        </div>
      @endforelse
    </div>
  </div>
</section>
