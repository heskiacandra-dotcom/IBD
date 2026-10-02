@extends('layouts.app')

@section('content')
<div class="layanan-page-wrapper" style="background-color: var(--bg-body, #F8FAFC); min-height: 100vh; padding: 24px 0 60px 0;">
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
      <span style="color: #0F172A; font-weight: 700;">Layanan Mandiri Warga</span>
    </nav>

    <!-- Header Card -->
    <header style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04); margin-bottom: 24px;">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
        <div style="max-width: 680px;">
          <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; color: #2563EB; margin-bottom: 10px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
            <span>Pelayanan Terpadu Mandiri RT {{ $nomorRt }} / RW {{ $nomorRw }}</span>
          </div>
          <h1 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0; line-height: 1.2;">
            Pusat Layanan Mandiri Digital Warga
          </h1>
          <p style="font-size: 0.875rem; color: #64748B; margin: 0; line-height: 1.5;">
            Pengurusan administrasi kependudukan lebih cepat tanpa harus tatap muka langsung: pengajuan surat pengantar resmi, pembayaran iuran kas RT, serta reservasi alat & inventaris bersama.
          </p>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
          <button type="button" class="btn btn-primary" data-modal="modalSurat" style="background: #2563EB; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; padding: 10px 18px; border-radius: 10px; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <span>+ Buat Surat Pengantar</span>
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
            <div style="font-size: 0.875rem; font-weight: 700; color: #065F46;">Aksi Berhasil Diproses!</div>
            <div style="font-size: 0.8125rem; color: #047857;">{{ session('success') }}</div>
          </div>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background: transparent; border: none; color: #059669; font-size: 1.25rem; cursor: pointer;">&times;</button>
      </div>
    @endif

    <!-- ====================================================================
         STATS PILLS ROW
         ==================================================================== -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
      
      <!-- Stat 1: Total Surat -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Surat Pengantar</span>
          <span style="background: #EFF6FF; color: #2563EB; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Total</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #0F172A; margin-top: 6px;">{{ $statsLayanan['total_surat'] }}</div>
        <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">{{ $statsLayanan['surat_disetujui'] }} disetujui • {{ $statsLayanan['surat_pending'] }} proses</div>
      </div>

      <!-- Stat 2: Tagihan Belum Lunas -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #B45309; text-transform: uppercase;">Tagihan Iuran</span>
          <span style="background: {{ $statsLayanan['tagihan_pending'] > 0 ? '#FEF2F2' : '#ECFDF5' }}; color: {{ $statsLayanan['tagihan_pending'] > 0 ? '#DC2626' : '#047857' }}; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">
            {{ $statsLayanan['tagihan_pending'] > 0 ? 'Tertunggak' : 'Lunas' }}
          </span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: {{ $statsLayanan['tagihan_pending'] > 0 ? '#DC2626' : '#059669' }}; margin-top: 6px;">
          {{ $statsLayanan['tagihan_pending'] }}
        </div>
        <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Total iuran terbayar: {{ $statsLayanan['total_terbayar'] }}</div>
      </div>

      <!-- Stat 3: Aset Inventaris -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Inventaris Siap Pakai</span>
          <span style="background: #FAF5FF; color: #9333EA; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Balai RT</span>
        </div>
        <div style="font-size: 1.75rem; font-weight: 800; color: #9333EA; margin-top: 6px;">{{ $statsLayanan['total_aset_tersedia'] }}</div>
        <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">Katalog barang siap dipinjam</div>
      </div>

      <!-- Stat 4: Identitas Warga Aktif -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; padding: 18px 20px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <span style="font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Akun Pemohon</span>
          <span style="background: #F1F5F9; color: #475569; font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 6px;">Terverifikasi</span>
        </div>
        <div style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin-top: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
          {{ $warga->Nama_Lengkap }}
        </div>
        <div style="font-size: 0.75rem; color: #64748B; margin-top: 4px;">NIK: {{ $warga->NIK }}</div>
      </div>

    </div>

    <!-- ====================================================================
         3 PRIMARY ACTION CARDS
         ==================================================================== -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 32px;">
      
      <!-- Card 1: Surat Pengantar -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04); display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="width: 48px; height: 48px; border-radius: 12px; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="16" x2="8" y1="13" y2="13"/>
              <line x1="16" x2="8" y1="17" y2="17"/>
            </svg>
          </div>
          <h3 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0;">Surat Pengantar RT Online</h3>
          <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 18px 0; line-height: 1.5;">
            Ajukan surat pengantar resmi ber-barcode tanda tangan digital untuk KTP, KK, SKCK, izin usaha, domisili, atau surat kematian.
          </p>
        </div>
        <button type="button" class="btn btn-primary" data-modal="modalSurat" style="width: 100%; font-size: 0.8125rem; padding: 10px 14px; border-radius: 8px; font-weight: 700; background: #2563EB; color: #FFFFFF; border: none; cursor: pointer;">
          + Ajukan Surat Baru
        </button>
      </div>

      <!-- Card 2: Bayar Iuran -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04); display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="width: 48px; height: 48px; border-radius: 12px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <rect width="20" height="14" x="2" y="5" rx="2"/>
              <line x1="2" x2="22" y1="10" y2="10"/>
            </svg>
          </div>
          <h3 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0;">Bayar Iuran Bulanan RT</h3>
          <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 18px 0; line-height: 1.5;">
            Cek rincian tagihan kas lingkungan keluarga Anda, bayar melalui transfer rekening resmi atau QRIS, lalu unggah bukti transfer.
          </p>
        </div>
        <button type="button" class="btn btn-primary" data-modal="modalIuran" style="width: 100%; font-size: 0.8125rem; padding: 10px 14px; border-radius: 8px; font-weight: 700; background: #059669; color: #FFFFFF; border: none; cursor: pointer;">
          💳 Cek & Bayar Iuran
        </button>
      </div>

      <!-- Card 3: Pinjam Inventaris -->
      <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 22px; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04); display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="width: 48px; height: 48px; border-radius: 12px; background: #FAF5FF; color: #9333EA; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            </svg>
          </div>
          <h3 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0;">Peminjaman Inventaris RT</h3>
          <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 18px 0; line-height: 1.5;">
            Pinjam tenda hajatan, kursi lipat, sound system, proyektor, serta alat potong rumput milik warga RT 05 tanpa ribet.
          </p>
        </div>
        <button type="button" class="btn btn-primary" data-modal="modalPinjam" style="width: 100%; font-size: 0.8125rem; padding: 10px 14px; border-radius: 8px; font-weight: 700; background: #9333EA; color: #FFFFFF; border: none; cursor: pointer;">
          📦 Pinjam Alat RT
        </button>
      </div>

    </div>

    <!-- ====================================================================
         TRACKING & RIWAYAT LAYANAN TABS
         ==================================================================== -->
    <section style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
      
      <!-- Tab Header Buttons -->
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0; padding-bottom: 16px; margin-bottom: 20px; gap: 12px;">
        <div>
          <h2 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0;">Riwayat & Aktivitas Layanan Saya</h2>
          <p style="font-size: 0.8125rem; color: #64748B; margin: 2px 0 0 0;">Pantau status dokumen permohonan, riwayat iuran, dan katalog aset</p>
        </div>

        <div style="display: flex; gap: 8px; background: #F1F5F9; padding: 4px; border-radius: 10px;">
          <button type="button" class="layanan-tab-btn active" onclick="switchLayananTab('tabSuratLayanan', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 700; border-radius: 8px; border: none; background: #FFFFFF; color: #2563EB; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08); cursor: pointer;">
            📄 Surat Pengantar ({{ $suratSaya->count() }})
          </button>
          <button type="button" class="layanan-tab-btn" onclick="switchLayananTab('tabTagihanLayanan', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; border: none; background: transparent; color: #64748B; cursor: pointer;">
            💳 Tagihan Iuran ({{ $tagihanSaya->count() }})
          </button>
          <button type="button" class="layanan-tab-btn" onclick="switchLayananTab('tabInventarisLayanan', this)" style="padding: 6px 14px; font-size: 0.8125rem; font-weight: 600; border-radius: 8px; border: none; background: transparent; color: #64748B; cursor: pointer;">
            📦 Katalog Inventaris ({{ $inventarisCatalog->count() }})
          </button>
        </div>
      </div>

      <!-- TAB 1: SURAT PENGANTAR SAYA -->
      <div id="tabSuratLayanan" class="layanan-tab-pane" style="display: block;">
        @if($suratSaya->count() > 0)
          <div style="display: flex; flex-direction: column; gap: 14px;">
            @foreach($suratSaya as $s)
              <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
                <div style="flex: 1; min-width: 260px;">
                  <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                    <span style="font-size: 0.75rem; font-family: monospace; font-weight: 700; color: #2563EB;">{{ $s['ticket_code'] }}</span>
                    <span>•</span>
                    <span style="font-size: 0.75rem; color: #64748B;">Diajukan: {{ $s['tanggal'] }}</span>
                    @if($s['is_acc'] && !empty($s['nomor_surat']))
                      <span style="font-size: 0.6875rem; font-family: monospace; font-weight: 700; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; padding: 1px 8px; border-radius: 6px;">
                        No: {{ $s['nomor_surat'] }}
                      </span>
                    @endif
                  </div>
                  <h4 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 4px 0;">{{ $s['jenis_surat'] }}</h4>
                  <p style="font-size: 0.8125rem; color: #475569; margin: 0 0 4px 0;">Keperluan: {{ $s['keperluan'] }}</p>
                  @if($s['is_rejected'] && !empty($s['catatan_penolakan']))
                    <div style="font-size: 0.75rem; color: #DC2626; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 6px; padding: 4px 8px; margin-top: 4px; display: inline-block;">
                      ⚠️ Alasan Penolakan: <em>"{{ $s['catatan_penolakan'] }}"</em>
                    </div>
                  @endif
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                  @if($s['is_acc'])
                    <span style="background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                      ✓ Disetujui RT
                    </span>
                    <a href="{{ $s['pdf_url'] }}" target="_blank" class="btn btn-sm btn-primary" style="font-size: 0.75rem; padding: 6px 14px; border-radius: 6px; background: #2563EB; color: #FFFFFF; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
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
          <div style="text-align: center; padding: 48px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#94A3B8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px auto;">
              <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
            <h4 style="font-size: 0.9375rem; font-weight: 700; color: #334155; margin: 0 0 4px 0;">Belum Ada Riwayat Surat Pengantar</h4>
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0 0 16px 0;">Ajukan surat pengantar resmi pertama Anda secara mandiri.</p>
            <button type="button" class="btn btn-primary" data-modal="modalSurat" style="background: #2563EB; color: #FFFFFF; font-size: 0.8125rem; padding: 8px 18px; border-radius: 8px; border: none; cursor: pointer;">
              + Ajukan Surat Pengantar Sekarang
            </button>
          </div>
        @endif
      </div>

      <!-- TAB 2: TAGIHAN IURAN SAYA -->
      <div id="tabTagihanLayanan" class="layanan-tab-pane" style="display: none;">
        @if($tagihanSaya->count() > 0)
          <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
              <thead>
                <tr style="border-bottom: 2px solid #E2E8F0; color: #475569;">
                  <th style="padding: 12px; font-weight: 700;">Periode & Tagihan</th>
                  <th style="padding: 12px; font-weight: 700;">Kategori</th>
                  <th style="padding: 12px; font-weight: 700;">Nominal</th>
                  <th style="padding: 12px; font-weight: 700;">Jatuh Tempo</th>
                  <th style="padding: 12px; font-weight: 700;">Status Verifikasi</th>
                  <th style="padding: 12px; font-weight: 700; text-align: right;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($tagihanSaya as $t)
                  <tr style="border-bottom: 1px solid #F1F5F9;">
                    <td style="padding: 14px 12px;">
                      <div style="font-weight: 700; color: #0F172A;">{{ $t['nama_tagihan'] }}</div>
                      <div style="font-size: 0.6875rem; color: #94A3B8; font-family: monospace;">{{ $t['ref_code'] }}</div>
                    </td>
                    <td style="padding: 14px 12px; color: #475569;">{{ $t['kategori'] }}</td>
                    <td style="padding: 14px 12px; font-weight: 700; color: {{ $t['is_lunas'] ? '#059669' : '#DC2626' }};">
                      {{ $t['nominal_formatted'] }}
                    </td>
                    <td style="padding: 14px 12px; color: #64748B;">{{ $t['tanggal'] }}</td>
                    <td style="padding: 14px 12px;">
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
                    <td style="padding: 14px 12px; text-align: right;">
                      @if($t['is_lunas'])
                        <span style="font-size: 0.75rem; color: #059669; font-weight: 700;">Terverifikasi</span>
                      @elseif($t['is_pending_verif'])
                        <span style="font-size: 0.75rem; color: #D97706; font-weight: 600;">Ditinjau Bendahara</span>
                      @elseif($t['is_rejected'])
                        <button type="button" class="btn btn-sm btn-danger" data-modal="modalIuran" style="font-size: 0.75rem; padding: 5px 12px; border-radius: 6px; background: #DC2626; color: #FFFFFF; border: none; cursor: pointer;">
                          Unggah Ulang
                        </button>
                      @else
                        <button type="button" class="btn btn-sm btn-success" data-modal="modalIuran" style="font-size: 0.75rem; padding: 5px 12px; border-radius: 6px; background: #059669; color: #FFFFFF; border: none; cursor: pointer;">
                          Bayar Sekarang
                        </button>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div style="text-align: center; padding: 48px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px;">
            <p style="font-size: 0.8125rem; color: #64748B; margin: 0;">Tidak ada catatan tagihan kas RT yang terdaftar untuk Kartu Keluarga ini.</p>
          </div>
        @endif
      </div>

      <!-- TAB 3: KATALOG INVENTARIS RT -->
      <div id="tabInventarisLayanan" class="layanan-tab-pane" style="display: none;">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px;">
          @forelse($inventarisCatalog as $inv)
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 18px; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                  <span style="font-size: 0.6875rem; font-family: monospace; font-weight: 700; color: #64748B;">{{ $inv['kode'] }}</span>
                  <span style="font-size: 0.6875rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; background: {{ $inv['is_tersedia'] ? '#ECFDF5' : '#FFFBEB' }}; color: {{ $inv['is_tersedia'] ? '#047857' : '#B45309' }}; border: 1px solid {{ $inv['is_tersedia'] ? '#A7F3D0' : '#FDE68A' }};">
                    {{ $inv['status_label'] }}
                  </span>
                </div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: #0F172A; margin: 0 0 6px 0;">{{ $inv['nama'] }}</h4>
                <div style="font-size: 0.75rem; color: #64748B; margin-bottom: 14px;">📍 Lokasi: {{ $inv['lokasi'] }}</div>
              </div>

              <div>
                <button type="button" class="btn btn-primary" data-modal="modalPinjam" style="width: 100%; font-size: 0.75rem; padding: 8px 12px; border-radius: 6px; background: #9333EA; color: #FFFFFF; border: none; cursor: pointer; font-weight: 600;">
                  Pinjam Alat Ini
                </button>
              </div>
            </div>
          @empty
            <div style="text-align: center; padding: 36px 20px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 12px; grid-column: 1 / -1;">
              <p style="font-size: 0.8125rem; color: #64748B; margin: 0;">Data inventaris belum terdaftar.</p>
            </div>
          @endforelse
        </div>
      </div>

    </section>

  </div>
</div>

@push('scripts')
<script>
  function switchLayananTab(targetId, btnElement) {
    document.querySelectorAll('.layanan-tab-pane').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.layanan-tab-btn').forEach(btn => {
      btn.style.background = 'transparent';
      btn.style.color = '#64748B';
      btn.style.boxShadow = 'none';
      btn.classList.remove('active');
    });

    const target = document.getElementById(targetId);
    if (target) {
      target.style.display = 'block';
    }

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
