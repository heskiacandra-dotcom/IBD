@extends('layouts.app')

@section('content')
<div class="transparansi-page-wrapper" style="background-color: var(--bg-body, #F8FAFC); min-height: 100vh; padding: 24px 0 60px 0;">
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
      <span style="color: #0F172A; font-weight: 700;">Transparansi Kas RT</span>
    </nav>

    <!-- Header Card -->
    <header style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04); margin-bottom: 24px;">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px;">
        <div style="max-width: 680px;">
          <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; color: #047857; margin-bottom: 10px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" x2="12" y1="2" y2="22"/>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            <span>Akuntabilitas & Keterbukaan Keuangan RT {{ $nomorRt }} / RW {{ $nomorRw }}</span>
          </div>
          <h1 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin: 0 0 6px 0; line-height: 1.2;">
            Transparansi Kas & Pembukuan Keuangan RT
          </h1>
          <p style="font-size: 0.875rem; color: #64748B; margin: 0; line-height: 1.5;">
            Laporan terbuka pengelolaan dana iuran warga. Seluruh penerimaan terverifikasi dan bukti belanja operasional lingkungan dicatat secara akuntabel demi integritas bersama.
          </p>
        </div>

        <div style="text-align: right;">
          <span style="font-size: 0.75rem; background: #F1F5F9; color: #475569; padding: 6px 12px; border-radius: 8px; font-weight: 700; border: 1px solid #E2E8F0; display: inline-flex; align-items: center; gap: 6px;">
            <span>Terakhir Diperbarui: {{ $transparansiData['update_terakhir'] }}</span>
          </span>
        </div>
      </div>
    </header>

    <!-- ====================================================================
         BIG FINANCIAL HEALTH & METRICS CARD
         ==================================================================== -->
    <div style="background: linear-gradient(135deg, #0F172A, #1E293B); border-radius: 20px; padding: 28px; color: #FFFFFF; box-shadow: 0 8px 24px rgba(15, 23, 42, 0.15); margin-bottom: 28px;">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 20px;">
        <div>
          <span style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94A3B8;">
            Total Saldo Kas RT {{ $nomorRt }} Saat Ini (Terverifikasi)
          </span>
          <div style="font-size: 2.5rem; font-weight: 900; color: #FFFFFF; margin-top: 4px; line-height: 1.1;">
            {{ $transparansiData['saldo_akhir'] }}
          </div>
          <div style="margin-top: 8px; display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 6px; background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.75rem; font-weight: 700; color: #34D399;">
            ✓ Status Kas: Likuid, Sehat & Surplus
          </div>
        </div>

        <!-- 3 Quick Metric Boxes -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 14px; min-width: 320px;">
          <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px;">
            <span style="font-size: 0.6875rem; color: #94A3B8; text-transform: uppercase; font-weight: 700; display: block;">Masuk Bulan Ini</span>
            <span style="font-size: 1.125rem; font-weight: 800; color: #34D399; margin-top: 4px; display: block;">{{ $transparansiData['masuk_bulan_ini'] }}</span>
          </div>

          <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px;">
            <span style="font-size: 0.6875rem; color: #94A3B8; text-transform: uppercase; font-weight: 700; display: block;">Belanja Bulan Ini</span>
            <span style="font-size: 1.125rem; font-weight: 800; color: #F87171; margin-top: 4px; display: block;">{{ $transparansiData['keluar_bulan_ini'] }}</span>
          </div>

          <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 14px;">
            <span style="font-size: 0.6875rem; color: #94A3B8; text-transform: uppercase; font-weight: 700; display: block;">Tingkat Kepatuhan</span>
            <span style="font-size: 1.125rem; font-weight: 800; color: #60A5FA; margin-top: 4px; display: block;">{{ $transparansiData['tingkat_kepatuhan'] }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ====================================================================
         ALOKASI ANGGARAN & REKENING RESMI
         ==================================================================== -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin-bottom: 32px;">
      
      <!-- Card: Alokasi Anggaran Belanja RT -->
      <section style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
        <div style="margin-bottom: 18px;">
          <h2 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0 0 4px 0;">Alokasi Pengeluaran Kas RT</h2>
          <p style="font-size: 0.75rem; color: #64748B; margin: 0;">Distribusi pos belanja bulanan berdasarkan musyawarah warga</p>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px;">
          @foreach($alokasiAnggaran as $alok)
            <div>
              <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem; margin-bottom: 4px;">
                <span style="font-weight: 700; color: #334155;">{{ $alok['kategori'] }}</span>
                <span style="font-weight: 800; color: #0F172A;">{{ $alok['persen'] }}%</span>
              </div>
              <div style="height: 8px; background: #F1F5F9; border-radius: 9999px; overflow: hidden; margin-bottom: 4px;">
                <div class="progress-bar-fill {{ $alok['class'] ?? '' }}" data-percent="{{ $alok['persen'] }}" data-color="{{ $alok['color'] }}" style="height: 100%; border-radius: 9999px;"></div>
              </div>
              <div style="font-size: 0.6875rem; color: #64748B; text-align: right;">Estimasi: {{ $alok['nominal'] }}</div>
            </div>
          @endforeach
        </div>
      </section>

      <!-- Card: Rekening Resmi Pembayaran Kas RT -->
      <section style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04); display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <div style="width: 36px; height: 36px; border-radius: 8px; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="5" rx="2"/>
                <line x1="2" x2="22" y1="10" y2="10"/>
              </svg>
            </div>
            <div>
              <h2 style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; margin: 0;">Rekening Resmi Kas Lingkungan</h2>
              <p style="font-size: 0.75rem; color: #64748B; margin: 0;">Rekening bank tunggal pengurus RT 05</p>
            </div>
          </div>

          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 16px; margin-bottom: 14px;">
            <div style="font-size: 0.6875rem; color: #64748B; text-transform: uppercase; font-weight: 700;">Nama Bank</div>
            <div style="font-size: 0.9375rem; font-weight: 800; color: #0F172A; margin-bottom: 8px;">{{ $rekeningResmi['bank'] }}</div>

            <div style="font-size: 0.6875rem; color: #64748B; text-transform: uppercase; font-weight: 700;">Nomor Rekening</div>
            <div style="font-size: 1.25rem; font-family: monospace; font-weight: 800; color: #2563EB; margin-bottom: 8px;">
              {{ $rekeningResmi['nomor_rekening'] }}
            </div>

            <div style="font-size: 0.6875rem; color: #64748B; text-transform: uppercase; font-weight: 700;">Atas Nama</div>
            <div style="font-size: 0.875rem; font-weight: 700; color: #334155;">{{ $rekeningResmi['atas_nama'] }}</div>
          </div>

          <div style="font-size: 0.75rem; color: #64748B; line-height: 1.4;">
            💡 <em>Setelah melakukan transfer, silakan konfirmasi melalui tombol di bawah untuk verifikasi otomatis oleh Bendahara RT.</em>
          </div>
        </div>

        <div style="margin-top: 16px;">
          <button type="button" class="btn btn-primary" data-modal="modalIuran" style="width: 100%; background: #059669; color: #FFFFFF; font-weight: 700; font-size: 0.8125rem; padding: 10px 14px; border-radius: 8px; border: none; cursor: pointer;">
            + Konfirmasi Pembayaran Iuran
          </button>
        </div>
      </section>

    </div>

    <!-- ====================================================================
         DAFTAR REALISASI PENGELUARAN KAS RT
         ==================================================================== -->
    <section style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; border-bottom: 1px solid #E2E8F0; padding-bottom: 16px; margin-bottom: 20px; gap: 12px;">
        <div>
          <h2 style="font-size: 1.125rem; font-weight: 800; color: #0F172A; margin: 0;">Catatan Realisasi Belanja & Pengeluaran Kas</h2>
          <p style="font-size: 0.8125rem; color: #64748B; margin: 2px 0 0 0;">Daftar transaksi pengeluaran operasional lingkungan yang telah disetujui</p>
        </div>

        <span style="font-size: 0.75rem; background: #EFF6FF; color: #2563EB; padding: 4px 10px; border-radius: 6px; font-weight: 700; border: 1px solid #DBEAFE;">
          Total Transaksi: {{ $pengeluaranList->count() }}
        </span>
      </div>

      <!-- Table of Expenditures -->
      <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; text-align: left;">
          <thead>
            <tr style="border-bottom: 2px solid #E2E8F0; color: #475569;">
              <th style="padding: 12px; font-weight: 700;">Ref & Tanggal</th>
              <th style="padding: 12px; font-weight: 700;">Kategori</th>
              <th style="padding: 12px; font-weight: 700;">Uraian Belanja / Keperluan</th>
              <th style="padding: 12px; font-weight: 700;">Nominal Belanja</th>
              <th style="padding: 12px; font-weight: 700;">Metode Bayar</th>
              <th style="padding: 12px; font-weight: 700; text-align: right;">Status Audit</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pengeluaranList as $p)
              <tr style="border-bottom: 1px solid #F1F5F9;">
                <td style="padding: 14px 12px;">
                  <span style="font-family: monospace; font-weight: 700; color: #2563EB;">{{ $p['ref_code'] }}</span>
                  <div style="font-size: 0.6875rem; color: #64748B; margin-top: 1px;">{{ $p['tanggal'] }}</div>
                </td>
                <td style="padding: 14px 12px;">
                  <span style="font-size: 0.6875rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; background: #F1F5F9; color: #334155; border: 1px solid #E2E8F0;">
                    {{ $p['kategori'] }}
                  </span>
                </td>
                <td style="padding: 14px 12px; font-weight: 600; color: #0F172A;">
                  {{ $p['keterangan'] }}
                </td>
                <td style="padding: 14px 12px; font-weight: 800; color: #DC2626;">
                  {{ $p['nominal_formatted'] }}
                </td>
                <td style="padding: 14px 12px; color: #64748B;">
                  {{ $p['metode'] }}
                </td>
                <td style="padding: 14px 12px; text-align: right;">
                  <span style="font-size: 0.6875rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; background: #ECFDF5; color: #047857; border: 1px solid #A7F3D0; display: inline-flex; align-items: center; gap: 4px;">
                    ✓ Terverifikasi LPJ
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" style="text-align: center; padding: 36px; color: #94A3B8;">
                  Belum ada catatan pengeluaran kas.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

  </div>
</div>
@endsection
