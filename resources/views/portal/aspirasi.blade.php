@extends('layouts.app')

@section('content')
<div class="aspirasi-page-wrapper" style="background-color: var(--bg-body, #F8FAFC); min-height: 100vh; padding: 24px 0 60px 0;">
  <div class="container">

    <!-- ====================================================================
         BREADCRUMB & HEADER SECTION
         ==================================================================== -->
    <nav style="display: flex; align-items: center; gap: 8px; font-size: 0.8125rem; color: #64748B; margin-bottom: 16px;">
      <a href="{{ route('portal.home') }}" style="color: #2563EB; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        <span>Portal Warga</span>
      </a>
      <span>/</span>
      <span style="color: #0F172A; font-weight: 700;">Kotak Aspirasi & Suara Warga</span>
    </nav>

    <!-- Header Card -->
    <header style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04); margin-bottom: 24px;">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
        <div style="max-width: 680px;">
          <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; color: #B45309; margin-bottom: 10px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            <span>Keterbukaan & Partisipasi Publik RT {{ $nomorRt }} / RW {{ $nomorRw }}</span>
          </div>
          <h1 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0; line-height: 1.2;">
            Kotak Aspirasi & Suara Warga Lingkungan
          </h1>
          <p style="font-size: 0.875rem; color: #64748B; margin: 0; line-height: 1.5;">
            Salurkan ide perbaikan fasilitas umum, laporan keamanan lingkungan, serta kritik konstruktif. Seluruh aspirasi akan ditelaah dan ditanggapi secara resmi dan transparan oleh Pengurus RT.
          </p>
        </div>

        <!-- Quick Action Trigger -->
        <div>
          <button type="button" onclick="focusAspirasiForm()" class="btn btn-primary" style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; padding: 12px 20px; border-radius: 12px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
              <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            <span>+ Tulis Aspirasi Baru</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Flash Notification -->
    @if(session('success'))
      <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: #059669; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 800;">
            ✓
          </div>
          <div>
            <div style="font-size: 0.875rem; font-weight: 700; color: #065F46;">Aspirasi Berhasil Disampaikan!</div>
            <div style="font-size: 0.8125rem; color: #047857;">{{ session('success') }}</div>
          </div>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background: transparent; border: none; color: #059669; font-size: 1.25rem; cursor: pointer;">&times;</button>
      </div>
    @endif

    <!-- ====================================================================
         STATS PILLS ROW
         ==================================================================== -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
      
      <!-- Stat 1: Total Aspirasi -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Total Masukan</span>
          <span style="background: #EFF6FF; color: #2563EB; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Warga</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #0F172A; margin-top: 6px;">{{ $stats['total'] }}</div>
        <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Aspirasi tercatat di sistem</div>
      </div>

      <!-- Stat 2: Ditanggapi Resmi RT -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #047857; text-transform: uppercase;">Ditanggapi Resmi</span>
          <span style="background: #ECFDF5; color: #047857; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">{{ $stats['response_rate'] }}% Selesai</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #059669; margin-top: 6px;">{{ $stats['responded'] }}</div>
        <div style="font-size: 0.75rem; color: #047857; margin-top: 4px;">Diberikan solusi oleh Ketua/Pengurus RT</div>
      </div>

      <!-- Stat 3: Menunggu Telaah -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #B45309; text-transform: uppercase;">Menunggu Telaah</span>
          <span style="background: #FFFBEB; color: #B45309; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Proses</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #D97706; margin-top: 6px;">{{ $stats['pending'] }}</div>
        <div style="font-size: 0.75rem; color: #B45309; margin-top: 4px;">Dalam antrean peninjauan pengurus</div>
      </div>

      <!-- Stat 4: Aspirasi Saya -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Aspirasi Saya</span>
          <span style="background: #F1F5F9; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">NIK Anda</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #2563EB; margin-top: 6px;">{{ $stats['mine'] }}</div>
        <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Usulan yang diajukan oleh Anda</div>
      </div>

    </div>

    <!-- ====================================================================
         MAIN SECTION: FORM & FEED
         ==================================================================== -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 24px;" class="aspirasi-grid-layout">
      
      <!-- SUBMISSION FORM CARD -->
      <section id="formAspirasiSection" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
          <div>
            <h2 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0;">Formulir Pengajuan Aspirasi Warga</h2>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 2px 0 0 0;">Kirimkan kritik, saran, atau pelaporan kendala lingkungan secara langsung.</p>
          </div>
          <div style="font-size: 0.75rem; color: #2563EB; background: #EFF6FF; border: 1px solid #DBEAFE; padding: 4px 10px; border-radius: 8px; font-weight: 600;">
            Pengirim: {{ $warga->Nama_Lengkap }}
          </div>
        </div>

        <form action="{{ route('portal.submit-aspirasi') }}" method="POST" id="formDirectAspirasi">
          @csrf
          <input type="hidden" name="nik" value="{{ $warga->NIK }}">

          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 16px;">
            <!-- Category Selector -->
            <div>
              <label for="kategoriInput" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Kategori Masalah / Usulan <span style="color: #DC2626;">*</span>
              </label>
              <select name="kategori" id="kategoriInput" required style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 0.875rem; color: #0F172A; background-color: #FFFFFF; font-family: inherit;">
                <option value="Fasilitas Umum">🛠️ Fasilitas Umum (Jalan, Lampu, Fasum)</option>
                <option value="Keamanan">🛡️ Keamanan & Ronda Lingkungan</option>
                <option value="Kebersihan">🧹 Kebersihan & Pengangkutan Sampah</option>
                <option value="Ketertiban">🤝 Ketertiban & Kerukunan Tetangga</option>
                <option value="Sosial">🎉 Kegiatan Warga & Sosial</option>
                <option value="Lainnya">💡 Usulan / Saran Lainnya</option>
              </select>
            </div>

            <!-- Subject / Title -->
            <div>
              <label for="judulInput" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
                Pokok Bahasan / Judul Singkat <span style="color: #DC2626;">*</span>
              </label>
              <input type="text" name="judul" id="judulInput" required placeholder="Contoh: Lampu Penerangan Jalan Blok C Padam" style="width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 0.875rem; color: #0F172A; font-family: inherit;">
            </div>
          </div>

          <!-- Content / Details -->
          <div style="margin-bottom: 16px;">
            <label for="isiInput" style="display: block; font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 6px;">
              Uraian Lengkap & Usulan Solusi <span style="color: #DC2626;">*</span>
            </label>
            <textarea name="isi" id="isiInput" rows="4" required placeholder="Jelaskan detail lokasi, waktu kejadian/kondisi, dampak yang dirasakan, serta usulan tindak lanjut yang diharapkan..." style="width: 100%; padding: 12px 14px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 0.875rem; color: #0F172A; font-family: inherit; line-height: 1.5;"></textarea>
          </div>

          <!-- Bottom Action -->
          <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px; border-top: 1px solid #F1F5F9; padding-top: 16px;">
            <div style="font-size: 0.75rem; color: #64748B; display: flex; align-items: center; gap: 6px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
              </svg>
              <span>Aspirasi Anda akan dicatat resmi pada database RT dan dipantau bersama seluruh warga.</span>
            </div>

            <button type="submit" id="btnKirimAspirasiDirect" style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; padding: 10px 24px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.2);">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
              </svg>
              <span>Kirim Aspirasi Sekarang</span>
            </button>
          </div>
        </form>
      </section>

      <!-- ====================================================================
           FEED & TANGGAPAN RESMI SECTION
           ==================================================================== -->
      <section style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
        
        <!-- Controls: Search & Filter Tabs -->
        <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px; border-bottom: 1px solid #F1F5F9; padding-bottom: 20px;">
          
          <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
            <div>
              <h2 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0;">Feed Suara Warga & Tanggapan RT</h2>
              <p style="font-size: 0.8125rem; color: #64748B; margin: 2px 0 0 0;">Transparansi diskusi publik lingkungan Harmoni Residence</p>
            </div>

            <!-- Search Input -->
            <div style="min-width: 260px; position: relative;">
              <input type="text" id="searchAspirasiInput" onkeyup="filterAspirasiCards()" placeholder="Cari topik atau nama warga..." style="width: 100%; padding: 8px 14px 8px 36px; border: 1px solid #CBD5E1; border-radius: 10px; font-size: 0.8125rem; color: #0F172A; font-family: inherit;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
              </svg>
            </div>
          </div>

          <!-- Status & Scope Filter Pills -->
          <div style="display: flex; flex-wrap: wrap; gap: 8px;" id="statusFilterPills">
            <button type="button" class="filter-tab-btn active" onclick="setStatusFilter('all', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 700; border-radius: 8px; border: 1px solid #DBEAFE; background: #2563EB; color: #FFFFFF; cursor: pointer;">
              Semua ({{ $stats['total'] }})
            </button>
            <button type="button" class="filter-tab-btn" onclick="setStatusFilter('responded', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer;">
              ✓ Ditanggapi Resmi ({{ $stats['responded'] }})
            </button>
            <button type="button" class="filter-tab-btn" onclick="setStatusFilter('pending', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer;">
              ⏳ Menunggu Telaah ({{ $stats['pending'] }})
            </button>
            <button type="button" class="filter-tab-btn" onclick="setStatusFilter('mine', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer;">
              👤 Aspirasi Saya ({{ $stats['mine'] }})
            </button>
          </div>

          <!-- Category Tags Filter -->
          <div style="display: flex; flex-wrap: wrap; gap: 6px;" id="categoryFilterPills">
            <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; align-self: center; margin-right: 4px;">Kategori:</span>
            <button type="button" class="cat-pill active" onclick="setCategoryFilter('all', this)" style="padding: 4px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 6px; border: 1px solid #CBD5E1; background: #0F172A; color: #FFFFFF; cursor: pointer;">
              Semua Kategori
            </button>
            <button type="button" class="cat-pill" onclick="setCategoryFilter('fasilitas', this)" style="padding: 4px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 6px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer;">
              🛠️ Fasilitas Umum
            </button>
            <button type="button" class="cat-pill" onclick="setCategoryFilter('keamanan', this)" style="padding: 4px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 6px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer;">
              🛡️ Keamanan & Ronda
            </button>
            <button type="button" class="cat-pill" onclick="setCategoryFilter('kebersihan', this)" style="padding: 4px 10px; font-size: 0.75rem; font-weight: 600; border-radius: 6px; border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; cursor: pointer;">
              🧹 Kebersihan
            </button>
          </div>

        </div>

        <!-- Aspirasi Feed Cards List -->
        <div id="aspirasiFeedList" style="display: flex; flex-direction: column; gap: 16px;">
          @forelse($aspirasiList as $item)
            <article class="aspirasi-item-card" 
                     data-id="{{ $item['id'] }}"
                     data-category="{{ $item['kategori_slug'] }}"
                     data-status="{{ $item['is_responded'] ? 'responded' : 'pending' }}"
                     data-mine="{{ $item['is_mine'] ? 'true' : 'false' }}"
                     data-search="{{ strtolower($item['judul'] . ' ' . $item['konten'] . ' ' . $item['author_name'] . ' ' . $item['kategori']) }}"
                     style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 14px; padding: 20px; transition: box-shadow 0.2s;">
              
              <!-- Top Row: Author, Date, Category Badge & Status -->
              <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                  <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $item['is_mine'] ? '#2563EB' : '#475569' }}; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9375rem; flex-shrink: 0;">
                    {{ $item['author_initials'] }}
                  </div>
                  <div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                      <span style="font-size: 0.9375rem; font-weight: 700; color: #0F172A;">
                        {{ $item['author_name'] }}
                      </span>
                      @if($item['is_mine'])
                        <span style="font-size: 0.6875rem; font-weight: 700; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; padding: 1px 6px; border-radius: 4px;">
                          Anda
                        </span>
                      @endif
                    </div>
                    <div style="font-size: 0.75rem; color: #64748B; margin-top: 1px;">
                      {{ $item['author_address'] }} • {{ $item['tanggal'] }}
                    </div>
                  </div>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                  <!-- Category Badge -->
                  <span style="font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #FFFFFF; border: 1px solid #CBD5E1; color: #334155;">
                    {{ $item['kategori'] }}
                  </span>

                  <!-- Status Badge -->
                  @if($item['is_responded'])
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 6px; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; display: inline-flex; align-items: center; gap: 4px;">
                      ✓ Ditanggapi Resmi RT
                    </span>
                  @else
                    <span style="font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 6px; background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; display: inline-flex; align-items: center; gap: 4px;">
                      ⏳ Menunggu Telaah RT
                    </span>
                  @endif
                </div>
              </div>

              <!-- Main Aspirasi Content -->
              <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0; line-height: 1.3;">
                {{ $item['judul'] }}
              </h3>
              <p style="font-size: 0.875rem; color: #334155; line-height: 1.6; margin: 0;">
                {{ $item['konten'] }}
              </p>

              <!-- OFFICIAL RT RESPONSE BOX (RESPON_ASPIRASI) -->
              @if($item['respon'])
                <div style="margin-top: 16px; background: #FFFFFF; border-left: 4px solid #2563EB; border-top: 1px solid #E2E8F0; border-right: 1px solid #E2E8F0; border-bottom: 1px solid #E2E8F0; border-radius: 0 12px 12px 0; padding: 14px 16px;">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                      </svg>
                      <strong style="font-size: 0.8125rem; color: #1D4ED8;">Tanggapan Resmi: {{ $item['respon']['responder'] }}</strong>
                    </div>
                    <span style="font-size: 0.6875rem; color: #64748B;">{{ $item['respon']['tanggal'] }}</span>
                  </div>
                  <div style="font-size: 0.875rem; color: #1E293B; line-height: 1.5; font-style: italic;">
                    "{{ $item['respon']['isi'] }}"
                  </div>
                </div>
              @else
                <div style="margin-top: 12px; padding: 8px 12px; background: #FFFFFF; border: 1px dashed #CBD5E1; border-radius: 8px; font-size: 0.75rem; color: #64748B; display: flex; align-items: center; gap: 8px;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                  </svg>
                  <span>Aspirasi ini telah masuk agenda telaah pengurus RT. Tanggapan resmi akan segera dipublikasikan di feed ini.</span>
                </div>
              @endif

            </article>
          @empty
            <div id="emptyAspirasiInitial" style="text-align: center; padding: 48px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 14px;">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px auto;">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
              </svg>
              <h4 style="font-size: 1rem; font-weight: 700; color: #334155; margin: 0 0 4px 0;">Belum Ada Aspirasi Publik</h4>
              <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 16px 0;">Jadilah warga pertama yang menyampaikan ide perbaikan untuk lingkungan RT 05.</p>
              <button type="button" onclick="focusAspirasiForm()" class="btn btn-primary" style="background: #2563EB; color: #FFFFFF; font-size: 0.8125rem; padding: 8px 18px; border-radius: 8px; border: none; cursor: pointer;">
                + Tulis Aspirasi Pertama
              </button>
            </div>
          @endforelse

          <!-- No Search Results Found Alert (Hidden by Default) -->
          <div id="noSearchResultsFound" style="display: none; text-align: center; padding: 40px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 14px;">
            <p style="font-size: 0.875rem; color: #64748B; margin: 0 0 8px 0;">Tidak ditemukan aspirasi yang cocok dengan kriteria filter atau pencarian Anda.</p>
            <button type="button" onclick="resetAspirasiFilters()" style="font-size: 0.75rem; color: #2563EB; background: #EFF6FF; border: 1px solid #DBEAFE; padding: 4px 12px; border-radius: 6px; font-weight: 700; cursor: pointer;">
              Reset Semua Filter
            </button>
          </div>

        </div>

      </section>

    </div>

  </div>
</div>

@push('scripts')
<script>
  let currentStatus = 'all';
  let currentCategory = 'all';

  function focusAspirasiForm() {
    const formSection = document.getElementById('formAspirasiSection');
    const input = document.getElementById('judulInput');
    if (formSection) {
      formSection.scrollIntoView({ behavior: 'smooth' });
    }
    if (input) {
      setTimeout(() => input.focus(), 400);
    }
  }

  function setStatusFilter(status, btnElement) {
    currentStatus = status;
    
    // Update active tab buttons
    document.querySelectorAll('#statusFilterPills .filter-tab-btn').forEach(btn => {
      btn.style.background = '#FFFFFF';
      btn.style.color = '#475569';
      btn.style.border = '1px solid #E2E8F0';
    });

    if (btnElement) {
      btnElement.style.background = '#2563EB';
      btnElement.style.color = '#FFFFFF';
      btnElement.style.border = '1px solid #DBEAFE';
    }

    filterAspirasiCards();
  }

  function setCategoryFilter(category, btnElement) {
    currentCategory = category;

    // Update active cat pills
    document.querySelectorAll('#categoryFilterPills .cat-pill').forEach(btn => {
      btn.style.background = '#FFFFFF';
      btn.style.color = '#475569';
      btn.style.border = '1px solid #E2E8F0';
    });

    if (btnElement) {
      btnElement.style.background = '#0F172A';
      btnElement.style.color = '#FFFFFF';
      btnElement.style.border = '1px solid #CBD5E1';
    }

    filterAspirasiCards();
  }

  function filterAspirasiCards() {
    const searchTerm = (document.getElementById('searchAspirasiInput')?.value || '').toLowerCase().trim();
    const cards = document.querySelectorAll('.aspirasi-item-card');
    let visibleCount = 0;

    cards.forEach(card => {
      const cardStatus = card.getAttribute('data-status');
      const cardCategory = card.getAttribute('data-category');
      const cardMine = card.getAttribute('data-mine') === 'true';
      const cardSearchText = card.getAttribute('data-search') || '';

      // Check Status Match
      let statusMatch = false;
      if (currentStatus === 'all') {
        statusMatch = true;
      } else if (currentStatus === 'responded') {
        statusMatch = (cardStatus === 'responded');
      } else if (currentStatus === 'pending') {
        statusMatch = (cardStatus === 'pending');
      } else if (currentStatus === 'mine') {
        statusMatch = cardMine;
      }

      // Check Category Match
      let catMatch = false;
      if (currentCategory === 'all') {
        catMatch = true;
      } else {
        catMatch = (cardCategory === currentCategory);
      }

      // Check Search Term Match
      let searchMatch = true;
      if (searchTerm.length > 0) {
        searchMatch = cardSearchText.includes(searchTerm);
      }

      if (statusMatch && catMatch && searchMatch) {
        card.style.display = 'block';
        visibleCount++;
      } else {
        card.style.display = 'none';
      }
    });

    const noResults = document.getElementById('noSearchResultsFound');
    if (noResults) {
      noResults.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
    }
  }

  function resetAspirasiFilters() {
    const searchInput = document.getElementById('searchAspirasiInput');
    if (searchInput) searchInput.value = '';

    const firstStatusBtn = document.querySelector('#statusFilterPills .filter-tab-btn');
    if (firstStatusBtn) setStatusFilter('all', firstStatusBtn);

    const firstCatBtn = document.querySelector('#categoryFilterPills .cat-pill');
    if (firstCatBtn) setCategoryFilter('all', firstCatBtn);
  }
</script>
@endpush
@endsection
