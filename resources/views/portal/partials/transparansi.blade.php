<section class="section transparency-section" id="transparansi">
  <div class="container">
    <div class="section-header">
      <span class="section-badge">Akuntabilitas Finansial Publik</span>
      <h2 class="section-title">Transparansi Kas Lingkungan</h2>
      <p class="section-description">
        Semua pemasukan iuran warga dan pengeluaran operasional dicatat secara terbuka demi menjaga amanah serta kepercayaan warga.
      </p>
    </div>

    <!-- ====================================================================
         TRANSPARENCY KAS BANNER: READ-ONLY SUMMARY
         (Current RT Cash Balance & Verified Monthly Expenses)
         ==================================================================== -->
    <div class="transparency-kas-banner" id="transparencyKasBanner" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%); border: 1px solid var(--slate-200); border-radius: var(--radius-xl); padding: 24px 28px; margin-bottom: 32px; box-shadow: var(--shadow-sm);">
      <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 20px; border-bottom: 1px solid var(--slate-200); padding-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background-color: var(--primary-50); color: var(--primary-600); display: flex; align-items: center; justify-content: center;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect width="20" height="14" x="2" y="5" rx="2"/>
              <line x1="2" x2="22" y1="10" y2="10"/>
            </svg>
          </div>
          <div>
            <div style="display: flex; align-items: center; gap: 8px;">
              <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--slate-900); margin: 0;">Transparansi Kas RT 05 / RW 01</h3>
              <span class="badge badge-emerald" style="font-size: 0.6875rem;">Read-Only Publik</span>
            </div>
            <p style="font-size: 0.8125rem; color: var(--slate-500); margin-top: 2px;">
              Ringkasan saldo kas riil RT dan pengeluaran bulanan terverifikasi otomatis dari pembukuan kas bendahara.
            </p>
          </div>
        </div>

        <div style="display: flex; align-items: center; gap: 8px;">
          <span class="badge badge-slate" style="font-size: 0.75rem;">
            Pembaruan Terakhir: {{ $keuangan['saldo_update'] ?? date('d F Y') }}
          </span>
          <span class="badge badge-emerald" style="font-size: 0.75rem;">
            Audit Kas: Cocok & Seimbang
          </span>
        </div>
      </div>

      <!-- Banner Key Summary Metric Columns -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
        <!-- Saldo Kas Riil RT -->
        <div style="background-color: #FFFFFF; border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 18px 20px; box-shadow: var(--shadow-xs);">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.5px;">Saldo Kas Riil Saat Ini</span>
            <span class="badge badge-emerald" style="font-size: 0.6875rem;">Aktif & Sah</span>
          </div>
          <div style="font-size: 1.625rem; font-weight: 800; color: var(--slate-900); letter-spacing: -0.02em;" id="bannerSaldoKas">
            {{ $keuangan['total_saldo'] ?? 'Rp 0' }}
          </div>
          <div style="font-size: 0.75rem; color: var(--emerald-700); margin-top: 4px; display: flex; align-items: center; gap: 4px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span>Dana cadangan operasional lingkungan siap guna</span>
          </div>
        </div>

        <!-- Pengeluaran Terverifikasi Bulan Ini -->
        <div style="background-color: #FFFFFF; border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 18px 20px; box-shadow: var(--shadow-xs);">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.5px;">Pengeluaran Terverifikasi Bulan Ini</span>
            <span class="badge badge-amber" style="font-size: 0.6875rem;">September 2026</span>
          </div>
          <div style="font-size: 1.625rem; font-weight: 800; color: var(--rose-600); letter-spacing: -0.02em;" id="bannerPengeluaranBulan">
            {{ $keuangan['pengeluaran_bulan'] ?? 'Rp 0' }}
          </div>
          <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 4px;">
            {{ $keuangan['pengeluaran_desc'] ?? 'Honor sekuriti, sampah & perawatan taman' }}
          </div>
        </div>

        <!-- Pemasukan Iuran Bulan Ini -->
        <div style="background-color: #FFFFFF; border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 18px 20px; box-shadow: var(--shadow-xs);">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.5px;">Pemasukan Iuran Terverifikasi</span>
            <span class="badge badge-emerald" style="font-size: 0.6875rem;">September 2026</span>
          </div>
          <div style="font-size: 1.625rem; font-weight: 800; color: var(--emerald-600); letter-spacing: -0.02em;" id="bannerPemasukanBulan">
            {{ $keuangan['pemasukan_bulan'] ?? 'Rp 0' }}
          </div>
          <div style="font-size: 0.75rem; color: var(--slate-500); margin-top: 4px;">
            {{ $keuangan['pemasukan_desc'] ?? 'Laporan iuran warga terverifikasi otomatis' }}
          </div>
        </div>
      </div>
    </div>

    <!-- 2 Columns: Pos Alokasi & Mutasi Terakhir -->
    <div class="transparency-layout">
      <!-- Alokasi Anggaran Bulanan -->
      <div class="box-container">
        <div class="box-title-row">
          <h3 class="box-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary-600);"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
            Alokasi Pos Iuran Kas
          </h3>
          <span class="badge badge-slate">Persentase</span>
        </div>

        @foreach($keuangan['alokasi'] as $item)
          <div class="allocation-item">
            <div class="allocation-label-row">
              <span>{{ $item['label'] }}</span>
              <span class="allocation-amount">{{ $item['persen'] }}% ({{ $item['nominal'] }})</span>
            </div>
            <div class="progress-bar-bg">
              <div class="progress-bar-fill" data-percent="{{ $item['persen'] }}" data-color="{{ $item['color'] }}"></div>
            </div>
          </div>
        @endforeach
      </div>

      <!-- Mutasi Kas Terbaru & Pengeluaran Terverifikasi -->
      <div class="box-container">
        <div class="box-title-row">
          <h3 class="box-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--emerald-600);"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Riwayat Mutasi Kas Terverifikasi
          </h3>
          <span class="badge badge-emerald">Realtime ERD</span>
        </div>

        <div class="ledger-list">
          @forelse($keuangan['mutasi'] as $trans)
            <div class="ledger-item">
              <div class="ledger-info">
                @if($trans['type'] === 'income')
                  <div class="ledger-icon" style="background-color: var(--emerald-50); color: var(--emerald-600);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </div>
                @else
                  <div class="ledger-icon" style="background-color: var(--rose-50); color: var(--rose-600);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                  </div>
                @endif
                <div class="ledger-details">
                  <div class="ledger-title">{{ $trans['title'] }}</div>
                  <div class="ledger-meta">{{ $trans['date'] }} • {{ $trans['desc'] }} • Ref: {{ $trans['ref'] }}</div>
                </div>
              </div>
              <div class="ledger-amount {{ $trans['type'] }}">{{ $trans['amount'] }}</div>
            </div>
          @empty
            <div style="padding: 24px; text-align: center; color: var(--slate-400); font-size: 0.8125rem;">
              Belum ada mutasi tercatat pada periode ini.
            </div>
          @endforelse
        </div>
      </div>
    </div>

    <!-- Verified Monthly Expenses Details Table (PENGELUARAN) -->
    @if(isset($pengeluaranList) && count($pengeluaranList) > 0)
      <div style="background-color: #FFFFFF; border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 20px 24px; margin-top: 24px; box-shadow: var(--shadow-xs);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
          <div>
            <h4 style="font-size: 0.9375rem; font-weight: 800; color: var(--slate-900);">Rincian Pengeluaran Operasional Terverifikasi (PENGELUARAN)</h4>
            <p style="font-size: 0.75rem; color: var(--slate-500);">Daftar pengeluaran kas yang disetujui Bendahara dan Ketua RT</p>
          </div>
          <span class="badge badge-slate">{{ count($pengeluaranList) }} Transaksi</span>
        </div>

        <div style="overflow-x: auto;">
          <table class="mutasi-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Keterangan Pengeluaran</th>
                <th>Kategori</th>
                <th>Tanggal Bayar</th>
                <th>Metode</th>
                <th style="text-align: right;">Jumlah Bayar</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pengeluaranList as $p)
                <tr>
                  <td><code style="font-size: 0.75rem;">#EXP-{{ str_pad($p->Pengeluaran_id, 4, '0', STR_PAD_LEFT) }}</code></td>
                  <td><strong style="color: var(--slate-900);">{{ $p->Keterangan }}</strong></td>
                  <td><span class="badge badge-slate">{{ $p->Kategori }}</span></td>
                  <td style="color: var(--slate-600); white-space: nowrap;">{{ \Illuminate\Support\Carbon::parse($p->Tgl_Bayar)->translatedFormat('d M Y') }}</td>
                  <td style="color: var(--slate-600);">{{ $p->Metode_Bayar }}</td>
                  <td style="text-align: right; color: var(--rose-600); font-weight: 800;">
                    -Rp {{ number_format($p->Jumlah_Bayar, 0, ',', '.') }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif

    <div class="transparency-footer-note" style="margin-top: 24px;">
      <div style="display: flex; align-items: center; gap: 8px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--emerald-600);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>Dikelola dan diverifikasi oleh <strong>{{ $keuangan['bendahara_nama'] ?? 'Bendahara RT' }}</strong> dan diawasi oleh Dewan Pengawas Warga.</span>
      </div>
      <div>Status Buku Kas: <strong style="color: var(--emerald-600);">Cocok & Seimbang</strong></div>
    </div>
  </div>
</section>
