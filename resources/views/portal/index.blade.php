@extends('layouts.app')

@section('content')
<div class="portal-citizen-wrapper" style="background-color: var(--bg-body, #F8FAFC); min-height: 100vh; padding: 24px 0 60px 0;">
  <div class="container">

    <!-- ====================================================================
         SECTION 1: COMPACT HEADER & PROFILE PILL
         ==================================================================== -->
    <header class="citizen-header-card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 20px 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04); margin-bottom: 24px;">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
        
        <!-- Profile Greeting & Pill -->
        <div style="display: flex; align-items: center; gap: 16px;">
          <div style="width: 52px; height: 52px; border-radius: 12px; background: linear-gradient(135deg, #2563EB, #1D4ED8); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);">
            {{ strtoupper(substr($warga->Nama_Lengkap ?? 'W', 0, 2)) }}
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
              <h1 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0; line-height: 1.2;">
                Halo, {{ $warga->Nama_Lengkap }}
              </h1>
              <span style="background: #EFF6FF; color: #2563EB; font-size: 0.75rem; font-weight: 700; padding: 3px 10px; border-radius: 9999px; border: 1px solid #DBEAFE;">
                KK: {{ $warga->ID_KK }} • RT {{ $nomorRt }}
              </span>
            </div>
            <div style="font-size: 0.8125rem; color: #64748B; margin-top: 4px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
              <span><strong>NIK:</strong> {{ $warga->NIK }}</span>
              <span>•</span>
              <span><strong>Alamat:</strong> {{ optional($warga->kartuKeluarga)->Alamat ?? ('RT ' . $nomorRt . ' / RW ' . $nomorRw) }}</span>
              @if($warga->no_telp)
                <span>•</span>
                <span><strong>Kontak:</strong> {{ $warga->no_telp }}</span>
              @endif
            </div>
          </div>
        </div>

        <!-- Direct Actions: Edit Profil & Logout -->
        <div style="display: flex; align-items: center; gap: 10px;">
          <!-- Edit Profil Trigger -->
          <button type="button" class="btn btn-subtle" data-modal="modalEditProfil" id="btnTriggerEditProfil" style="font-size: 0.8125rem; padding: 8px 16px; border-radius: 10px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #334155; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
            </svg>
            <span>Edit Profil</span>
          </button>

          <!-- Logout Button -->
          <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
            @csrf
            <button type="submit" class="btn btn-subtle" id="btnCitizenLogout" style="font-size: 0.8125rem; padding: 8px 16px; border-radius: 10px; border: 1px solid #FEE2E2; background: #FEF2F2; color: #DC2626; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
              </svg>
              <span>Keluar</span>
            </button>
          </form>
        </div>

      </div>
    </header>

    <!-- ====================================================================
         SECTION 2: PRIMARY QUICK-ACTION MATRIX (4 Prominent Service Cards)
         ==================================================================== -->
    <section class="quick-action-matrix-section" style="margin-bottom: 32px;" id="layanan">
      <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 18px;">
        <div>
          <h2 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0 0 4px 0;">Layanan Mandiri Warga</h2>
          <p style="font-size: 0.8125rem; color: #64748B; margin: 0;">Akses cepat administrasi resmi dan fasilitas lingkungan tanpa antre</p>
        </div>
        <a href="{{ route('portal.layanan') }}" style="font-size: 0.8125rem; font-weight: 700; color: #2563EB; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: color 0.2s;">
          <span>Lihat Semua Layanan</span>
          <span>&rarr;</span>
        </a>
      </div>

      <div class="quick-matrix-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
        
        <!-- Card 1: Ajukan Surat Pengantar -->
        <div class="matrix-card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <div style="width: 40px; height: 40px; border-radius: 10px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                  <polyline points="14 2 14 8 20 8"/>
                  <line x1="16" y1="13" x2="8" y2="13"/>
                  <line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
              </div>
              <span style="font-size: 0.6875rem; font-weight: 700; color: #2563EB; background: #EFF6FF; border: 1px solid #DBEAFE; padding: 2px 8px; border-radius: 9999px;">
                Online 24 Jam
              </span>
            </div>
            <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0;">Ajukan Surat Pengantar</h3>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 18px 0; line-height: 1.5; min-height: 38px;">
              Permohonan surat domisili, SKCK, perubahan KK, hingga surat izin usaha terbit resmi dengan verifikasi RT.
            </p>
          </div>
          <div>
            <button type="button" class="btn btn-primary" data-modal="modalSurat" id="btnCardAjukanSurat" style="width: 100%; font-size: 0.8125rem; padding: 10px 14px; border-radius: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15); transition: background 0.2s;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
              <span>Buat Pengajuan Surat</span>
            </button>
          </div>
        </div>

        <!-- Card 2: Bayar Iuran RT -->
        <div class="matrix-card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <div style="width: 40px; height: 40px; border-radius: 10px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="20" height="14" x="2" y="5" rx="2"/>
                  <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
              </div>
              @if(count($tagihanPending) > 0)
                <span style="font-size: 0.6875rem; font-weight: 700; background: #FEF3C7; color: #B45309; border: 1px solid #FDE68A; padding: 2px 8px; border-radius: 9999px;">
                  {{ count($tagihanPending) }} Tagihan Baru
                </span>
              @else
                <span style="font-size: 0.6875rem; font-weight: 700; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; padding: 2px 8px; border-radius: 9999px;">
                  ✓ Iuran Lunas
                </span>
              @endif
            </div>
            <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0;">Bayar Iuran RT</h3>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 18px 0; line-height: 1.5; min-height: 38px;">
              Bayar iuran kebersihan & keamanan keluarga via QRIS atau transfer bank. Upload bukti bayar instan.
            </p>
          </div>
          <div>
            <button type="button" class="btn btn-primary" data-modal="modalIuran" id="btnCardBayarIuran" style="width: 100%; font-size: 0.8125rem; padding: 10px 14px; border-radius: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15); transition: background 0.2s;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
              <span>Bayar Iuran Sekarang</span>
            </button>
          </div>
        </div>

        <!-- Card 3: Peminjaman Alat -->
        <div class="matrix-card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <div style="width: 40px; height: 40px; border-radius: 10px; background: #F5F3FF; color: #7C3AED; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                  <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                  <line x1="12" y1="22.08" x2="12" y2="12"/>
                </svg>
              </div>
              <span style="font-size: 0.6875rem; font-weight: 700; color: #6D28D9; background: #F5F3FF; border: 1px solid #DDD6FE; padding: 2px 8px; border-radius: 9999px;">
                Tersedia
              </span>
            </div>
            <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0;">Peminjaman Alat</h3>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 18px 0; line-height: 1.5; min-height: 38px;">
              Pinjam fasilitas warga RT: tenda balai, kursi hajatan, proyektor rapat, dan sound system resmi.
            </p>
          </div>
          <div>
            <button type="button" class="btn btn-primary" data-modal="modalPinjam" id="btnCardPinjamAlat" style="width: 100%; font-size: 0.8125rem; padding: 10px 14px; border-radius: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15); transition: background 0.2s;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
              <span>Pinjam Inventaris</span>
            </button>
          </div>
        </div>

        <!-- Card 4: Suara Warga / Aspirasi -->
        <div class="matrix-card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03); display: flex; flex-direction: column; justify-content: space-between; transition: all 0.2s ease;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
              <div style="width: 40px; height: 40px; border-radius: 10px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
              </div>
              <span style="font-size: 0.6875rem; font-weight: 700; color: #B45309; background: #FEF3C7; border: 1px solid #FDE68A; padding: 2px 8px; border-radius: 9999px;">
                Respons Cepat
              </span>
            </div>
            <h3 style="font-size: 1rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0;">Suara Warga / Aspirasi</h3>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 18px 0; line-height: 1.5; min-height: 38px;">
              Sampaikan ide, saran pembangunan, atau laporan fasilitas umum langsung ke Ketua RT secara transparan.
            </p>
          </div>
          <div>
            <button type="button" class="btn btn-primary" data-modal="modalAspirasi" id="btnCardKirimAspirasi" style="width: 100%; font-size: 0.8125rem; padding: 10px 14px; border-radius: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer; box-shadow: 0 2px 6px rgba(37, 99, 235, 0.15); transition: background 0.2s;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              <span>Kirim Aspirasi RT</span>
            </button>
          </div>
        </div>

      </div>
    </section>

    <!-- ====================================================================
         SECTION 3: ACTIVITY & STATUS TRACKING TAB (Citizen's Personal History)
         ==================================================================== -->
    <section class="citizen-activity-section" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04); margin-bottom: 32px;" id="status-saya">
      
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0; padding-bottom: 16px; margin-bottom: 20px; gap: 12px;">
        <div>
          <h2 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0;">Aktivitas & Riwayat Saya</h2>
          <p style="font-size: 0.8125rem; color: #64748B; margin: 2px 0 0 0;">Pantau status dokumen, histori pembayaran iuran, dan tanggapan aduan pribadi Anda</p>
        </div>

        <!-- Tab Switchers -->
        <div class="citizen-tabs-wrapper" style="display: inline-flex; background: #F1F5F9; padding: 4px; border-radius: 10px; gap: 4px;" role="tablist">
          <button type="button" class="citizen-tab-btn active" onclick="switchCitizenTab('tabSuratSaya', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 700; border-radius: 8px; border: none; background: #FFFFFF; color: #2563EB; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08); cursor: pointer; display: flex; align-items: center; gap: 6px;">
            <span>📄 Surat Saya</span>
            <span style="background: #EFF6FF; color: #2563EB; font-size: 0.6875rem; padding: 1px 6px; border-radius: 9999px;">{{ $suratSaya->count() }}</span>
          </button>
          <button type="button" class="citizen-tab-btn" onclick="switchCitizenTab('tabTagihanSaya', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; border: none; background: transparent; color: #64748B; cursor: pointer; display: flex; align-items: center; gap: 6px;">
            <span>💳 Tagihan Iuran Saya</span>
            <span style="background: #E2E8F0; color: #475569; font-size: 0.6875rem; padding: 1px 6px; border-radius: 9999px;">{{ $tagihanSaya->count() }}</span>
          </button>
          <button type="button" class="citizen-tab-btn" onclick="switchCitizenTab('tabAspirasiSaya', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; border: none; background: transparent; color: #64748B; cursor: pointer; display: flex; align-items: center; gap: 6px;">
            <span>📢 Aspirasi Saya</span>
            <span style="background: #E2E8F0; color: #475569; font-size: 0.6875rem; padding: 1px 6px; border-radius: 9999px;">{{ $aspirasiSaya->count() }}</span>
          </button>
        </div>
      </div>

      <!-- Tab 1: Surat Saya -->
      <div id="tabSuratSaya" class="citizen-tab-pane" style="display: block;">
        @if($suratSaya->count() > 0)
          <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($suratSaya as $s)
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px;">
                <div style="flex: 1; min-width: 250px;">
                  <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                    <span style="font-size: 0.75rem; font-family: monospace; font-weight: 700; color: #2563EB;">{{ $s['ticket_code'] }}</span>
                    <span>•</span>
                    <span style="font-size: 0.75rem; color: #64748B;">{{ $s['tanggal'] }}</span>
                    @if($s['is_acc'] && !empty($s['nomor_surat']))
                      <span style="font-size: 0.6875rem; font-family: monospace; font-weight: 700; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; padding: 1px 8px; border-radius: 6px;">
                        No: {{ $s['nomor_surat'] }}
                      </span>
                    @endif
                  </div>
                  <h4 style="font-size: 0.9375rem; font-weight: 700; color: #0F172A; margin: 0 0 4px 0;">{{ $s['jenis_surat'] }}</h4>
                  <p style="font-size: 0.8125rem; color: #475569; margin: 0 0 4px 0;">Keperluan: {{ $s['keperluan'] }}</p>
                  @if($s['is_rejected'] && !empty($s['catatan_penolakan']))
                    <div style="font-size: 0.75rem; color: #DC2626; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 6px; padding: 4px 8px; margin-top: 4px; display: inline-block;">
                      ⚠️ Alasan Penolakan: <em>"{{ $s['catatan_penolakan'] }}"</em>
                    </div>
                  @endif
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                  @if($s['is_acc'])
                    <span style="background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                      ✓ Disetujui RT
                    </span>
                    <a href="{{ $s['pdf_url'] }}" target="_blank" class="btn btn-sm btn-primary" style="font-size: 0.75rem; padding: 6px 12px; border-radius: 6px; background: #2563EB; color: #FFFFFF; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                      </svg>
                      <span>Unduh PDF Resmi</span>
                    </a>
                  @elseif($s['is_rejected'])
                    <span style="background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                      ✕ Permohonan Ditolak
                    </span>
                  @else
                    <span style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                      ⏳ Menunggu Verifikasi RT
                    </span>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div style="text-align: center; padding: 36px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 10px auto;">
              <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
            <h4 style="font-size: 0.9375rem; font-weight: 700; color: #334155; margin: 0 0 4px 0;">Belum Ada Riwayat Surat</h4>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 14px 0;">Anda belum pernah mengajukan surat pengantar resmi ke pengurus RT.</p>
            <button type="button" class="btn btn-primary" data-modal="modalSurat" style="font-size: 0.8125rem; padding: 8px 16px; border-radius: 8px; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer;">
              + Buat Pengajuan Surat Pertama
            </button>
          </div>
        @endif
      </div>

      <!-- Tab 2: Tagihan Iuran Saya -->
      <div id="tabTagihanSaya" class="citizen-tab-pane" style="display: none;">
        @if($tagihanSaya->count() > 0)
          <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
              <thead>
                <tr style="border-bottom: 2px solid #E2E8F0; color: #475569;">
                  <th style="padding: 10px 12px; font-weight: 700;">Periode & Tagihan</th>
                  <th style="padding: 10px 12px; font-weight: 700;">Kategori</th>
                  <th style="padding: 10px 12px; font-weight: 700;">Nominal</th>
                  <th style="padding: 10px 12px; font-weight: 700;">Jatuh Tempo</th>
                  <th style="padding: 10px 12px; font-weight: 700;">Status Verifikasi</th>
                  <th style="padding: 10px 12px; font-weight: 700; text-align: right;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tagihanSaya as $t)
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 12px;">
                      <div style="font-weight: 700; color: #0F172A;">{{ $t['nama_tagihan'] }}</div>
                      <div style="font-size: 0.6875rem; color: #94A3B8; font-family: monospace;">{{ $t['ref_code'] }}</div>
                    </td>
                    <td style="padding: 12px; color: #475569;">{{ $t['kategori'] }}</td>
                    <td class="{{ $t['is_lunas'] ? 'tagihan-nominal-lunas' : 'tagihan-nominal-belum' }}" style="padding: 12px; font-weight: 700;">
                      {{ $t['nominal_formatted'] }}
                    </td>
                    <td style="padding: 12px; color: #64748B;">{{ $t['tanggal'] }}</td>
                    <td style="padding: 12px;">
                      @if($t['is_lunas'])
                        <span style="background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; font-size: 0.6875rem; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                          ✓ Lunas Sah
                        </span>
                      @elseif($t['is_pending_verif'])
                        <span style="background: #FFFBEB; color: #B45309; border: 1px solid #FDE68A; font-size: 0.6875rem; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                          ⏳ Menunggu Verifikasi
                        </span>
                      @elseif($t['is_rejected'])
                        <div>
                          <span style="background: #FEF2F2; color: #DC2626; border: 1px solid #FECACA; font-size: 0.6875rem; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                            ✕ Pembayaran Ditolak
                          </span>
                          @if(!empty($t['catatan_penolakan']))
                            <div style="font-size: 0.6875rem; color: #DC2626; margin-top: 3px;">
                              Catatan: {{ $t['catatan_penolakan'] }}
                            </div>
                          @endif
                        </div>
                      @else
                        <span style="background: #F1F5F9; color: #475569; border: 1px solid #CBD5E1; font-size: 0.6875rem; font-weight: 700; padding: 3px 8px; border-radius: 6px;">
                          Belum Dibayar
                        </span>
                      @endif
                    </td>
                    <td style="padding: 12px; text-align: right;">
                      @if($t['is_lunas'])
                        <span style="font-size: 0.75rem; color: #059669; font-weight: 700;">Terverifikasi</span>
                      @elseif($t['is_pending_verif'])
                        <span style="font-size: 0.75rem; color: #D97706; font-weight: 600;">Ditinjau Bendahara</span>
                      @elseif($t['is_rejected'])
                        <button type="button" class="btn btn-sm btn-danger" data-modal="modalIuran" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; background: #DC2626; color: #FFFFFF; border: none; cursor: pointer;">
                          Unggah Ulang
                        </button>
                      @else
                        <button type="button" class="btn btn-sm btn-success" data-modal="modalIuran" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; background: #059669; color: #FFFFFF; border: none; cursor: pointer;">
                          Bayar
                        </button>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div style="text-align: center; padding: 36px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px;">
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0;">Tidak ada catatan tagihan kas RT yang terdaftar untuk Kartu Keluarga ini.</p>
          </div>
        @endif
      </div>

      <!-- Tab 3: Aspirasi Saya -->
      <div id="tabAspirasiSaya" class="citizen-tab-pane" style="display: none;">
        @if($aspirasiSaya->count() > 0)
          <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach($aspirasiSaya as $asp)
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                  <span style="font-size: 0.75rem; color: #64748B;">Diajukan: {{ $asp['tanggal'] }}</span>
                  <span class="aspirasi-badge {{ $asp['is_ditanggapi'] ? 'aspirasi-badge-ditanggapi' : 'aspirasi-badge-menunggu' }}">
                    {{ $asp['status_label'] }}
                  </span>
                </div>
                <p style="font-size: 0.875rem; color: #1E293B; margin: 0 0 12px 0; line-height: 1.5;">
                  {{ $asp['isi'] }}
                </p>

                @if($asp['respon'])
                  <div style="background: #EFF6FF; border-left: 3px solid #2563EB; padding: 10px 14px; border-radius: 0 8px 8px 0; margin-top: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                      <strong style="font-size: 0.75rem; color: #1D4ED8;">Tanggapan Resmi ({{ $asp['respon']['responder'] }})</strong>
                      <span style="font-size: 0.6875rem; color: #64748B;">{{ $asp['respon']['tanggal'] }}</span>
                    </div>
                    <div style="font-size: 0.8125rem; color: #1E293B; font-style: italic;">
                      "{{ $asp['respon']['isi'] }}"
                    </div>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div style="text-align: center; padding: 36px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px;">
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 12px 0;">Anda belum pernah mengirimkan aspirasi atau pengaduan warga.</p>
            <button type="button" class="btn btn-primary" data-modal="modalAspirasi" style="font-size: 0.8125rem; padding: 8px 16px; border-radius: 8px; background: #D97706; color: #FFFFFF; border: none; cursor: pointer;">
              + Sampaikan Aspirasi / Ide
            </button>
          </div>
        @endif
      </div>

    </section>

    <!-- ====================================================================
         SECTION 4: FINANCIAL TRANSPARENCY & COMMUNITY EVENTS
         ==================================================================== -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;" id="transparansi">
      
      <!-- Financial Transparency Widget (Aggregate Summary Only) -->
      <section class="transparency-summary-card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div>
            <h2 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0;">Transparansi Kas Lingkungan RT {{ $nomorRt }}</h2>
            <p style="font-size: 0.75rem; color: #64748B; margin: 2px 0 0 0;">Ringkasan agregat kesehatan saldo kas dan mutasi terverifikasi</p>
          </div>
          <span style="font-size: 0.6875rem; background: #EFF6FF; color: #2563EB; padding: 3px 8px; border-radius: 6px; font-weight: 700; border: 1px solid #DBEAFE;">
            Diperbarui: {{ $transparansiKas['update_terakhir'] }}
          </span>
        </div>

        <!-- Big Metric Pill: Real Cash Balance -->
        <div style="background: linear-gradient(135deg, #1E293B, #0F172A); border-radius: 14px; padding: 18px 20px; color: #FFFFFF; margin-bottom: 16px;">
          <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94A3B8; font-weight: 700;">Total Saldo Kas RT {{ $nomorRt }}</span>
          <div style="font-size: 1.75rem; font-weight: 800; margin-top: 4px; color: #FFFFFF;">
            {{ $transparansiKas['total_saldo'] }}
          </div>
          <div style="display: flex; gap: 16px; margin-top: 10px; font-size: 0.75rem; border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 10px;">
            <span style="color: #34D399; font-weight: 600;">Masuk Bulan Ini: {{ $transparansiKas['total_masuk_bulan'] }}</span>
            <span style="color: #F87171; font-weight: 600;">Keluar Bulan Ini: {{ $transparansiKas['total_keluar_bulan'] }}</span>
          </div>
        </div>

        <!-- High-level Allocation Categories (No Personal Data Shown) -->
        <div style="margin-top: 14px;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em;">Alokasi Pengeluaran Kas RT:</span>
          <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
            @foreach($transparansiKas['alokasi'] as $alok)
              <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.75rem; margin-bottom: 3px;">
                  <span style="color: #334155; font-weight: 600;">{{ $alok['kategori'] }}</span>
                  <span style="font-weight: 700; color: #0F172A;">{{ $alok['persen'] }}%</span>
                </div>
                <div style="height: 6px; background: #F1F5F9; border-radius: 9999px; overflow: hidden;">
                  <div class="progress-bar-fill {{ $alok['class'] ?? '' }}" data-percent="{{ $alok['persen'] }}" data-color="{{ $alok['color'] }}"></div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </section>

      <!-- Community Events & Agenda Lingkungan -->
      <section class="community-events-card" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);" id="kegiatan">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
          <div>
            <h2 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0;">Agenda Kegiatan Lingkungan</h2>
            <p style="font-size: 0.75rem; color: #64748B; margin: 2px 0 0 0;">Jadwal gotong royong, posyandu, dan rapat RT</p>
          </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
          @forelse($agendaList as $agenda)
            @php
              $agStatus = $agenda['status'] ?? 'Mendatang';
              $agClass = $agStatus === 'Sedang Berlangsung' ? 'is-berlangsung' : ($agStatus === 'Selesai' ? 'is-selesai' : 'is-mendatang');
            @endphp
            <div style="display: flex; gap: 14px; align-items: center; padding: 12px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px;">
              <!-- Date Badge -->
              <div class="agenda-date-badge {{ $agClass }}">
                <span class="agenda-date-day">{{ $agenda['day'] }}</span>
                <span class="agenda-date-month">{{ $agenda['month'] }}</span>
              </div>
              <div style="flex: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 6px; margin-bottom: 2px;">
                  <h4 style="font-size: 0.875rem; font-weight: 700; color: #0F172A; margin: 0;">{{ $agenda['title'] }}</h4>
                  <span class="agenda-status-pill {{ $agClass }}">
                    {{ $agStatus }}
                  </span>
                </div>
                <div style="font-size: 0.75rem; color: #64748B;">
                  <span>📍 {{ $agenda['location'] }}</span>
                  @if(!empty($agenda['full_date']))
                    <span> • {{ $agenda['full_date'] }}</span>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div style="text-align: center; padding: 24px; color: #94A3B8; font-size: 0.8125rem;">
              Belum ada agenda kegiatan RT yang dijadwalkan dalam waktu dekat.
            </div>
          @endforelse
        </div>
      </section>

    </div>

    <!-- ====================================================================
         SECTION 5: SUARA & ASPIRASI PUBLIK RT (Anchor id="aspirasi")
         ==================================================================== -->
    <section style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04); margin-top: 32px;" id="aspirasi">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 14px;">
        <div>
          <div style="display: inline-flex; align-items: center; gap: 6px; padding: 3px 8px; background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 6px; font-size: 0.6875rem; font-weight: 700; color: #B45309; margin-bottom: 6px;">
            <span>📢 Suara Warga & Diskusi Terbuka</span>
          </div>
          <h2 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0;">Aspirasi Lingkungan & Tanggapan Resmi RT</h2>
          <p style="font-size: 0.8125rem; color: #64748B; margin: 2px 0 0 0;">Laporan fasilitas, masukan keamanan, dan respon resmi dari Ketua RT 05</p>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
          <a href="{{ route('portal.aspirasi') }}" style="font-size: 0.8125rem; font-weight: 700; color: #2563EB; background: #EFF6FF; border: 1px solid #DBEAFE; padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
            <span>Buka Halaman Aspirasi Lengkap</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="9 18 15 12 9 6"/>
            </svg>
          </a>
          <button type="button" class="btn btn-primary" data-modal="modalAspirasi" style="font-size: 0.8125rem; font-weight: 700; background: #D97706; color: #FFFFFF; padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer;">
            + Kirim Aspirasi
          </button>
        </div>
      </div>

      <!-- Feed Grid -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
        @forelse($aspirasiFeed ?? [] as $item)
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; margin-bottom: 8px;">
                <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A;">{{ $item['author_name'] }}</div>
                <span style="font-size: 0.6875rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: #FFFFFF; border: 1px solid #CBD5E1; color: #475569;">
                  {{ $item['kategori'] }}
                </span>
              </div>
              <h4 style="font-size: 0.875rem; font-weight: 700; color: #1E293B; margin: 0 0 6px 0;">{{ $item['judul'] }}</h4>
              <p style="font-size: 0.8125rem; color: #475569; margin: 0; line-height: 1.4;">
                {{ \Illuminate\Support\Str::limit($item['konten'], 120) }}
              </p>
            </div>

            @if($item['respon'])
              <div style="margin-top: 12px; padding: 8px 10px; background: #EFF6FF; border-left: 3px solid #2563EB; border-radius: 0 6px 6px 0; font-size: 0.75rem;">
                <div style="font-weight: 700; color: #1D4ED8; margin-bottom: 2px;">Tanggapan: {{ $item['respon']['responder'] }}</div>
                <div style="color: #1E293B; font-style: italic;">"{{ \Illuminate\Support\Str::limit($item['respon']['isi'], 90) }}"</div>
              </div>
            @else
              <div style="margin-top: 12px; font-size: 0.6875rem; color: #94A3B8;">
                ⏳ Menunggu telaah pengurus RT
              </div>
            @endif
          </div>
        @empty
          <div style="text-align: center; padding: 24px; color: #94A3B8; font-size: 0.8125rem; grid-column: 1 / -1;">
            Belum ada aspirasi publik.
          </div>
        @endforelse
      </div>
    </section>

  </div>
</div>

@push('scripts')
<script>
  function switchCitizenTab(targetId, btnElement) {
    // Hide all tab panes
    document.querySelectorAll('.citizen-tab-pane').forEach(el => el.style.display = 'none');
    
    // Deactivate all buttons
    document.querySelectorAll('.citizen-tab-btn').forEach(btn => {
      btn.style.background = 'transparent';
      btn.style.color = '#64748B';
      btn.style.boxShadow = 'none';
      btn.classList.remove('active');
    });

    // Show target pane
    const targetPane = document.getElementById(targetId);
    if (targetPane) {
      targetPane.style.display = 'block';
    }

    // Activate clicked button
    if (btnElement) {
      btnElement.style.background = '#FFFFFF';
      btnElement.style.color = '#2563EB';
      btnElement.style.boxShadow = '0 1px 3px rgba(15, 23, 42, 0.08)';
      btnElement.classList.add('active');
    }
  }
</script>
@endpush
@endsection
