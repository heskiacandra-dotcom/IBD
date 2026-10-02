@extends('layouts.app')

@section('content')
<div class="agenda-page-wrapper" style="background-color: var(--bg-body, #F8FAFC); min-height: 100vh; padding: 24px 0 60px 0;">
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
      <span style="color: #0F172A; font-weight: 700;">Agenda & Kegiatan Lingkungan</span>
    </nav>

    <!-- Header Card -->
    <header style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04); margin-bottom: 24px;">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
        <div style="max-width: 680px;">
          <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; color: #2563EB; margin-bottom: 10px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
              <line x1="16" x2="16" y1="2" x2="16" y2="6"/>
              <line x1="8" x2="8" y1="2" x2="8" y2="6"/>
              <line x1="3" x2="21" y1="10" x2="10"/>
            </svg>
            <span>Kalender Lingkungan Kompak RT {{ $nomorRt }} / RW {{ $nomorRw }}</span>
          </div>
          <h1 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0; line-height: 1.2;">
            Agenda Kegiatan & Jadwal Lingkungan RT
          </h1>
          <p style="font-size: 0.875rem; color: #64748B; margin: 0; line-height: 1.5;">
            Pantau jadwal kerja bakti gotong royong, pemeriksaan kesehatan posyandu balita & lansia, rapat musyawarah warga, serta jadwal ronda malam siskamling lingkungan Harmoni Residence.
          </p>
        </div>

        <div>
          <button type="button" onclick="focusUsulKegiatan()" class="btn btn-primary" style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; padding: 12px 20px; border-radius: 12px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>+ Usulkan Kegiatan Warga</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Flash Alert -->
    @if(session('success'))
      <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 32px; height: 32px; border-radius: 8px; background: #059669; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 800;">
            ✓
          </div>
          <div>
            <div style="font-size: 0.875rem; font-weight: 700; color: #065F46;">Usulan Berhasil Dikirim!</div>
            <div style="font-size: 0.8125rem; color: #047857;">{{ session('success') }}</div>
          </div>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background: transparent; border: none; color: #059669; font-size: 1.25rem; cursor: pointer;">&times;</button>
      </div>
    @endif

    <!-- ====================================================================
         STATS ROW
         ==================================================================== -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
      
      <!-- Stat 1: Total Agenda -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Total Kegiatan</span>
          <span style="background: #EFF6FF; color: #2563EB; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Tahun Ini</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #0F172A; margin-top: 6px;">{{ $statsAgenda['total'] }}</div>
        <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Agenda terdaftar resmi di kalender RT</div>
      </div>

      <!-- Stat 2: Mendatang -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #047857; text-transform: uppercase;">Akan Datang</span>
          <span style="background: #ECFDF5; color: #047857; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Siap Hadir</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #059669; margin-top: 6px;">{{ $statsAgenda['mendatang'] }}</div>
        <div style="font-size: 0.75rem; color: #047857; margin-top: 4px;">Kegiatan yang akan segera terlaksana</div>
      </div>

      <!-- Stat 3: Wajib KK -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #B45309; text-transform: uppercase;">Wajib Per KK</span>
          <span style="background: #FFFBEB; color: #B45309; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Gotong Royong</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #D97706; margin-top: 6px;">{{ $statsAgenda['wajib'] }}</div>
        <div style="font-size: 0.75rem; color: #B45309; margin-top: 4px;">Kerja bakti & pembersihan saluran</div>
      </div>

      <!-- Stat 4: Selesai -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Terlaksana</span>
          <span style="background: #F1F5F9; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Arsip</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #475569; margin-top: 6px;">{{ $statsAgenda['selesai'] }}</div>
        <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Kegiatan sukses terselenggara</div>
      </div>

    </div>

    <!-- ====================================================================
         MAIN 2-COLUMN LAYOUT
         ==================================================================== -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 24px; align-items: start;">
      
      <!-- LEFT COLUMN: KALENDER KEGIATAN RT -->
      <section style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0; padding-bottom: 16px; margin-bottom: 20px; gap: 12px;">
          <div>
            <h2 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0;">Daftar Agenda Kegiatan RT 05</h2>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 2px 0 0 0;">Partisipasi aktif warga demi lingkungan yang bersih dan aman</p>
          </div>

          <!-- Filter Tabs -->
          <div style="display: flex; gap: 6px; background: #F1F5F9; padding: 4px; border-radius: 10px;">
            <button type="button" class="agenda-tab-btn active" onclick="filterAgendaCards('all', this)" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 700; border-radius: 6px; border: none; background: #FFFFFF; color: #2563EB; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08); cursor: pointer;">
              Semua ({{ $statsAgenda['total'] }})
            </button>
            <button type="button" class="agenda-tab-btn" onclick="filterAgendaCards('mendatang', this)" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 600; border-radius: 6px; border: none; background: transparent; color: #64748B; cursor: pointer;">
              Mendatang ({{ $statsAgenda['mendatang'] }})
            </button>
            <button type="button" class="agenda-tab-btn" onclick="filterAgendaCards('selesai', this)" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 600; border-radius: 6px; border: none; background: transparent; color: #64748B; cursor: pointer;">
              Selesai ({{ $statsAgenda['selesai'] }})
            </button>
          </div>
        </div>

        <!-- Feed List -->
        <div id="agendaFeedList" style="display: flex; flex-direction: column; gap: 16px;">
          @forelse($agendaList as $agenda)
            <article class="agenda-item-card" data-past="{{ $agenda['is_past'] ? 'true' : 'false' }}" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px; display: flex; gap: 16px; align-items: flex-start; transition: all 0.2s;">
              <!-- Date Block Badge -->
              <div style="min-width: 58px; height: 64px; background: {{ $agenda['is_past'] ? '#F1F5F9' : '#EFF6FF' }}; border: 1px solid {{ $agenda['is_past'] ? '#CBD5E1' : '#DBEAFE' }}; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; flex-shrink: 0;">
                <span style="font-size: 1.25rem; font-weight: 800; color: {{ $agenda['is_past'] ? '#64748B' : '#2563EB' }}; line-height: 1;">{{ $agenda['day'] }}</span>
                <span style="font-size: 0.6875rem; font-weight: 700; color: {{ $agenda['is_past'] ? '#94A3B8' : '#3B82F6' }}; text-transform: uppercase; margin-top: 2px;">{{ $agenda['month'] }}</span>
                <span style="font-size: 0.5625rem; color: #94A3B8;">{{ $agenda['year'] }}</span>
              </div>

              <!-- Content -->
              <div style="flex: 1;">
                <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 6px;">
                  <span style="font-size: 0.6875rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; background: {{ $agenda['is_wajib'] ? '#ECFDF5' : '#EFF6FF' }}; color: {{ $agenda['is_wajib'] ? '#047857' : '#1D4ED8' }}; border: 1px solid {{ $agenda['is_wajib'] ? '#A7F3D0' : '#DBEAFE' }};">
                    {{ $agenda['badge'] }}
                  </span>
                  <span style="font-size: 0.75rem; color: {{ $agenda['is_past'] ? '#64748B' : '#059669' }}; font-weight: 600;">
                    {{ $agenda['status_label'] }}
                  </span>
                </div>

                <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0;">
                  {{ $agenda['title'] }}
                </h3>

                <div style="font-size: 0.75rem; color: #64748B; margin-bottom: 8px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                  <span>📍 {{ $agenda['location'] }}</span>
                  <span>•</span>
                  <span>🗓️ {{ $agenda['full_date'] }}</span>
                </div>

                <p style="font-size: 0.8125rem; color: #334155; line-height: 1.5; margin: 0;">
                  {{ $agenda['desc'] ?? 'Pelaksanaan kegiatan bersama seluruh warga Harmoni Residence.' }}
                </p>
              </div>
            </article>
          @empty
            <div style="text-align: center; padding: 40px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px;">
              <p style="font-size: 0.8125rem; color: #64748B; margin: 0;">Belum ada jadwal kegiatan yang tercatat.</p>
            </div>
          @endforelse
        </div>
      </section>

      <!-- RIGHT COLUMN: JADWAL RONDA & FORMULIR USUL KEGIATAN -->
      <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- JADWAL SISKAMLING / RONDA MALAM -->
        <section style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 14px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
              </svg>
            </div>
            <div>
              <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0;">Jadwal Ronda Malam Siskamling</h3>
              <p style="font-size: 0.75rem; color: #64748B; margin: 1px 0 0 0;">Piket keamanan swakarsa warga RT 05</p>
            </div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 10px; max-height: 380px; overflow-y: auto; padding-right: 4px;">
            @foreach($jadwalRonda as $ronda)
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                  <strong style="font-size: 0.8125rem; color: #0F172A;">{{ $ronda['hari'] }}</strong>
                  <span style="font-size: 0.6875rem; font-family: monospace; font-weight: 700; color: #2563EB; background: #EFF6FF; padding: 2px 6px; border-radius: 4px;">
                    {{ $ronda['jam'] }}
                  </span>
                </div>
                <div style="font-size: 0.75rem; color: #334155; margin-bottom: 2px;">
                  <strong>Koordinator:</strong> {{ $ronda['koordinator'] }}
                </div>
                <div style="font-size: 0.6875rem; color: #64748B;">
                  <strong>Petugas:</strong> {{ $ronda['anggota'] }}
                </div>
                <div style="font-size: 0.6875rem; color: #94A3B8; margin-top: 4px;">
                  📍 {{ $ronda['lokasi'] }}
                </div>
              </div>
            @endforeach
          </div>

          <div style="margin-top: 14px; padding: 10px 12px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; font-size: 0.75rem; color: #1D4ED8; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
            </svg>
            <span>Pos Siaga Piket Sekuriti: <strong>0821-9876-5432</strong></span>
          </div>
        </section>

        <!-- FORMULIR USUL KEGIATAN -->
        <section id="formUsulKegiatanSection" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
          <div style="margin-bottom: 16px;">
            <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 4px 0;">Usulkan Ide Kegiatan Baru</h3>
            <p style="font-size: 0.75rem; color: #64748B; margin: 0;">Punya ide acara kebersamaan atau program lingkungan? Usulkan langsung ke pengurus RT.</p>
          </div>

          <form action="{{ route('portal.usul-kegiatan') }}" method="POST" id="formDirectUsulKegiatan">
            @csrf
            
            <div style="margin-bottom: 12px;">
              <label for="namaKegiatanInput" style="display: block; font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 4px;">
                Nama Kegiatan yang Diusulkan <span style="color: #DC2626;">*</span>
              </label>
              <input type="text" name="nama_kegiatan" id="namaKegiatanInput" required placeholder="Contoh: Senam Sehat Minggu Pagi & Cek Gula Darah" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.8125rem; font-family: inherit;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
              <div>
                <label for="tglKegiatanInput" style="display: block; font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 4px;">
                  Perkiraan Tanggal <span style="color: #DC2626;">*</span>
                </label>
                <input type="date" name="tgl_kegiatan" id="tglKegiatanInput" required style="width: 100%; padding: 8px 10px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.8125rem; font-family: inherit;">
              </div>
              <div>
                <label for="lokasiKegiatanInput" style="display: block; font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 4px;">
                  Usulan Lokasi <span style="color: #DC2626;">*</span>
                </label>
                <input type="text" name="lokasi" id="lokasiKegiatanInput" required placeholder="Balai Warga / Lapangan" style="width: 100%; padding: 8px 10px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.8125rem; font-family: inherit;">
              </div>
            </div>

            <div style="margin-bottom: 14px;">
              <label for="deskripsiKegiatanInput" style="display: block; font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 4px;">
                Tujuan & Rincian Konsep Singkat
              </label>
              <textarea name="deskripsi" id="deskripsiKegiatanInput" rows="3" placeholder="Jelaskan tujuan, sasaran peserta (bapak/ibu/anak), serta estimasi kebutuhan acara..." style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.8125rem; font-family: inherit; line-height: 1.4;"></textarea>
            </div>

            <button type="submit" style="width: 100%; background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; padding: 10px 14px; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.2);">
              <span>Kirim Usulan Kegiatan RT</span>
            </button>
          </form>
        </section>

      </div>

    </div>

  </div>
</div>

@push('scripts')
<script>
  function focusUsulKegiatan() {
    const section = document.getElementById('formUsulKegiatanSection');
    const input = document.getElementById('namaKegiatanInput');
    if (section) {
      section.scrollIntoView({ behavior: 'smooth' });
    }
    if (input) {
      setTimeout(() => input.focus(), 400);
    }
  }

  function filterAgendaCards(filter, btnElement) {
    document.querySelectorAll('.agenda-tab-btn').forEach(btn => {
      btn.style.background = 'transparent';
      btn.style.color = '#64748B';
      btn.style.boxShadow = 'none';
      btn.classList.remove('active');
    });

    if (btnElement) {
      btnElement.style.background = '#FFFFFF';
      btnElement.style.color = '#2563EB';
      btnElement.style.boxShadow = '0 1px 3px rgba(15, 23, 42, 0.08)';
      btnElement.classList.add('active');
    }

    const cards = document.querySelectorAll('.agenda-item-card');
    cards.forEach(card => {
      const isPast = card.getAttribute('data-past') === 'true';
      if (filter === 'all') {
        card.style.display = 'flex';
      } else if (filter === 'mendatang') {
        card.style.display = (!isPast) ? 'flex' : 'none';
      } else if (filter === 'selesai') {
        card.style.display = (isPast) ? 'flex' : 'none';
      }
    });
  }
</script>
@endpush
@endsection
