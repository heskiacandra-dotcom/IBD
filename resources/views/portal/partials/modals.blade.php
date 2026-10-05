<!-- ==========================================================================
     MODAL: SURAT PENGANTAR ONLINE (calls sp_ajukan_surat_warga)
     ========================================================================== -->
<div class="modal-overlay" id="modalSurat" role="dialog" aria-modal="true">
  <div class="modal-container">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Surat Pengantar Online</h3>
          <div class="modal-subtitle">Proses cepat verifikasi resmi RT via stored procedure `sp_ajukan_surat_warga`</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formSuratPengantar" action="{{ route('portal.submit-surat') }}" method="POST">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Jenis Surat Pengantar <span class="req">*</span></label>
          <select class="form-select" id="jenisSurat" name="jenis_surat" required>
            <option value="">-- Pilih Jenis Surat Pengantar --</option>
            <option value="Surat Keterangan Domisili Warga">Surat Keterangan Domisili Warga</option>
            <option value="Surat Pengantar Pembuatan / Perubahan KTP & KK">Surat Pengantar Pembuatan / Perubahan KTP & KK</option>
            <option value="Surat Pengantar SKCK Kepolisian">Surat Pengantar SKCK Kepolisian</option>
            <option value="Surat Keterangan Usaha / UMKM Warga">Surat Keterangan Usaha / UMKM Warga</option>
            <option value="Surat Keterangan Belum Menikah / Menikah">Surat Keterangan Belum Menikah / Menikah</option>
            <option value="Surat Keterangan Kurang Mampu (Beasiswa/Bansos)">Surat Keterangan Kurang Mampu (Beasiswa/Bansos)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Nama Lengkap Pemohon (Sesuai KTP) <span class="req">*</span></label>
          <input type="text" class="form-input" id="namaPemohon" name="nama" value="{{ $warga->Nama_Lengkap ?? ($activeCitizen->nama ?? '') }}" placeholder="Contoh: Hendra Wijaya" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Nomor Induk Kependudukan (NIK) <span class="req">*</span></label>
            <input type="text" class="form-input" id="nikPemohon" name="nik" value="{{ $warga->NIK ?? ($activeCitizen->nik ?? '') }}" placeholder="16 digit NIK" maxlength="16" required>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Rumah / Blok <span class="req">*</span></label>
            <input type="text" class="form-input" id="blokPemohon" name="blok" value="{{ optional($warga->kartuKeluarga ?? null)->Alamat ?? ($activeCitizen->alamat ?? '') }}" placeholder="Misal: Blok B-12" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor WhatsApp Aktif <span class="req">*</span></label>
          <input type="tel" class="form-input" id="waPemohon" name="no_wa" value="{{ $warga->no_telp ?? ($activeCitizen->whatsapp ?? '081234567890') }}" placeholder="08xxxxxxxxxx (untuk notifikasi surat)" required>
          <div class="form-hint">Surat yang disetujui akan terbit otomatis dengan QR Code dan dapat diunduh langsung.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Keperluan Pengajuan Surat <span class="req">*</span></label>
          <textarea class="form-textarea" id="keperluanSurat" name="keperluan" placeholder="Jelaskan secara singkat keperluan Anda, misal: Persyaratan administrasi pekerjaan / Perpanjangan dokumen..." required></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitSurat">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
          Kirim Pengajuan Surat
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: BAYAR IURAN BULANAN (Checks pending bills from PEMASUKAN & allows receipt upload)
     ========================================================================== -->
<div class="modal-overlay" id="modalIuran" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 680px; max-height: 88vh; display: flex; flex-direction: column; overflow: hidden;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--emerald-50); color: var(--emerald-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Bayar Iuran Bulanan</h3>
          <div class="modal-subtitle">Pengecekan tagihan kas dari tabel PEMASUKAN & upload bukti transfer</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formKonfirmasiIuran" action="{{ route('portal.konfirmasi-iuran') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex: 1; min-height: 0; overflow: hidden; margin: 0;">
      @csrf
      <div class="modal-body" style="overflow-y: auto; flex: 1; min-height: 0; padding: 20px 24px;">
        <!-- Household ID & Privacy Scope Info -->
        <input type="hidden" id="searchHouseNumber" name="house" value="{{ optional($warga->kartuKeluarga ?? null)->Alamat ?? ($activeCitizen->alamat ?? 'Harmoni') }}">
        
        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.75rem; color: #64748B; font-weight: 600; text-transform: uppercase;">Tagihan Khusus Kartu Keluarga:</div>
            <div style="font-size: 0.9375rem; font-weight: 800; color: #0F172A;">
              KK: {{ $warga->ID_KK ?? ($activeCitizen->no_kk ?? '-') }} • {{ $warga->Nama_Lengkap ?? ($activeCitizen->nama ?? 'Warga') }}
            </div>
          </div>
          <span style="font-size: 0.6875rem; font-weight: 700; background: #EFF6FF; color: #2563EB; border: 1px solid #DBEAFE; padding: 3px 8px; border-radius: 9999px;">
            Data Terenkripsi & Terisolasi
          </span>
        </div>

        <!-- Monthly Lifecycle & Accumulation / Double Alert Banner -->
        @if(isset($billingSummary) && ($billingSummary['is_double'] ?? false))
          <div style="background: linear-gradient(135deg, #FFF1F2 0%, #FFE4E6 100%); border: 1.5px solid #F43F5E; border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; display: flex; gap: 12px; align-items: flex-start; box-shadow: 0 2px 8px rgba(244, 63, 94, 0.1);">
            <div style="width: 34px; height: 34px; border-radius: 8px; background: #E11D48; color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 800; font-size: 0.875rem;">
              {{ $billingSummary['multiplier'] ?? 2 }}x
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                <strong style="color: #9F1239; font-size: 0.9375rem;">Tagihan Akumulatif (Double): {{ $billingSummary['total_nominal_formatted'] }}</strong>
                <span class="badge badge-rose" style="font-size: 0.625rem; font-weight: 800;">{{ $billingSummary['unpaid_count'] }} Bulan Tertunggak</span>
              </div>
              <p style="color: #BE123C; font-size: 0.8125rem; margin: 0; line-height: 1.45;">
                Iuran dibayar setiap bulan dan <strong>warga bebas membayar di tanggal berapa saja selama bulan berjalan</strong>. Karena tagihan bulan sebelumnya belum diselesaikan dan sudah berganti bulan, tagihan otomatis menumpuk/double. Anda dapat melunasi seluruhnya sekaligus.
              </p>
            </div>
          </div>
        @elseif(isset($billingSummary) && ($billingSummary['unpaid_count'] ?? 0) === 1)
          <div style="background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 10px; padding: 10px 14px; margin-bottom: 14px; font-size: 0.8125rem; color: #1E40AF; display: flex; align-items: center; justify-content: space-between;">
            <span>📅 <strong>Siklus Bulanan:</strong> Bebas bayar di tanggal berapa saja bulan ini sebelum berganti bulan.</span>
            <span class="badge badge-emerald" style="font-size: 0.6875rem;">1 Bulan Berjalan</span>
          </div>
        @endif

        <!-- Pending Bills List from PEMASUKAN -->
        @if(isset($tagihanPending) && count($tagihanPending) > 0)
          <div style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
              <label class="form-label" style="font-weight: 700; color: var(--slate-800); margin: 0;">Pilih Pembayaran Iuran</label>
              @if(count($tagihanPending) > 1)
                <span style="font-size: 0.75rem; color: #64748B;">Tersedia bayar double sekaligus atau per bulan</span>
              @endif
            </div>

            <div class="pending-bills-list" style="display: flex; flex-direction: column; gap: 8px; max-height: 240px; overflow-y: auto; border: 1px solid var(--slate-200); border-radius: var(--radius-md); padding: 8px; background-color: var(--slate-50);">
              
              {{-- Opsi 1: Bayar Semua / Double Sekaligus jika lebih dari 1 tagihan --}}
              @if(count($tagihanPending) > 1 && isset($billingSummary))
                <label style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: linear-gradient(135deg, #FFF1F2 0%, #FFFFFF 100%); border: 2px solid #F43F5E; border-radius: var(--radius-sm); cursor: pointer; box-shadow: 0 1px 3px rgba(244, 63, 94, 0.08);">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="radio" name="tagihan_id" value="all" data-nominal="{{ $billingSummary['total_nominal'] }}" checked>
                    <div>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <strong style="font-size: 0.875rem; color: #9F1239;">Bayar Semua Tagihan ({{ $billingSummary['unpaid_count'] }} Bulan Sekaligus)</strong>
                        <span class="badge badge-rose" style="font-size: 0.625rem;">Rekomendasi</span>
                      </div>
                      <div style="font-size: 0.6875rem; color: #BE123C;">Melunasi seluruh tunggakan bulan lalu & tagihan bulan berjalan</div>
                    </div>
                  </div>
                  <div style="text-align: right;">
                    <span style="font-size: 1rem; font-weight: 800; color: #E11D48;">{{ $billingSummary['total_nominal_formatted'] }}</span>
                  </div>
                </label>
              @endif

              {{-- Opsi Per Bulan --}}
              @foreach($tagihanPending as $tp)
                @php
                  $isOverdue = \Illuminate\Support\Carbon::parse($tp->tgl_jatuh_tmp)->lt(now()->startOfMonth());
                @endphp
                <label style="display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; background: #ffffff; border: 1px solid var(--slate-200); border-radius: var(--radius-sm); cursor: pointer;">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="radio" name="tagihan_id" value="{{ $tp->Tagihan_Id }}" data-nominal="{{ $tp->nominal }}" {{ (count($tagihanPending) === 1 && $loop->first) ? 'checked' : '' }}>
                    <div>
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <strong style="font-size: 0.8125rem; color: var(--slate-900);">{{ $tp->nama_tagihan }}</strong>
                        @if($isOverdue)
                          <span class="badge badge-rose" style="font-size: 0.5625rem; padding: 1px 6px;">Tertunggak (Bulan Lalu)</span>
                        @else
                          <span class="badge badge-emerald" style="font-size: 0.5625rem; padding: 1px 6px;">Bulan Berjalan</span>
                        @endif
                      </div>
                      <div style="font-size: 0.6875rem; color: var(--slate-500);">
                        Batas Waktu: {{ \Illuminate\Support\Carbon::parse($tp->tgl_jatuh_tmp)->locale('id')->translatedFormat('d F Y') }} (Bebas bayar kapan saja)
                      </div>
                    </div>
                  </div>
                  <div style="text-align: right;">
                    <span style="font-size: 0.875rem; font-weight: 800; color: var(--slate-800);">Rp {{ number_format($tp->nominal, 0, ',', '.') }}</span>
                    <div>
                      <span class="badge {{ in_array(strtolower($tp->status_tagihan), ['pending_approval', 'menunggu verifikasi', 'menunggu']) ? 'badge-amber' : ($isOverdue ? 'badge-rose' : 'badge-amber') }}" style="font-size: 0.625rem;">
                        {{ in_array(strtolower($tp->status_tagihan), ['pending_approval', 'menunggu verifikasi', 'menunggu']) ? 'Menunggu Verifikasi' : ($isOverdue ? 'Belum Dibayar (Double)' : 'Belum Dibayar') }}
                      </span>
                    </div>
                  </div>
                </label>
              @endforeach
            </div>
          </div>
        @else
          <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 14px 16px; margin-bottom: 16px; text-align: center;">
            <strong style="color: #047857; font-size: 0.875rem;">Semua Tagihan Keluarga Anda Lunas!</strong>
            <p style="color: #065F46; font-size: 0.75rem; margin: 4px 0 0 0;">Tidak ada tagihan tertunggak pada pembukuan kas RT saat ini.</p>
          </div>
        @endif

        <!-- Result Box & Payment Simulator -->
        <div class="iuran-result-box" id="iuranResultBox" style="display: block;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <div>
              <strong id="iuranOwnerName" style="font-size: 1rem; color: var(--slate-900);">{{ $warga->Nama_Lengkap ?? ($activeCitizen->nama ?? 'Warga RT 05') }}</strong>
              <div id="iuranHouseAddress" style="font-size: 0.8125rem; color: var(--slate-500);">{{ optional($warga->kartuKeluarga ?? null)->Alamat ?? ($activeCitizen->alamat ?? 'Harmoni Residence') }}</div>
            </div>
            <div id="iuranStatusBadge">
              @if(isset($tagihanPending) && count($tagihanPending) > 0)
                @if(isset($billingSummary) && ($billingSummary['is_double'] ?? false))
                  <span class="badge badge-rose">Tertunggak (Double)</span>
                @else
                  <span class="badge badge-amber">Belum Lunas</span>
                @endif
              @else
                <span class="badge badge-emerald">Lunas</span>
              @endif
            </div>
          </div>

          <table class="iuran-details-table">
            <tbody>
              <tr>
                <td>Iuran Keamanan & Ronda</td>
                <td>Rp 45.000 / bulan</td>
              </tr>
              <tr>
                <td>Iuran Kebersihan & Pengangkutan Sampah</td>
                <td>Rp 30.000 / bulan</td>
              </tr>
              <tr style="font-weight: 700; color: var(--slate-900); font-size: 0.9375rem;">
                <td>Total Tagihan yang Dipilih</td>
                <td id="iuranTotalAmount" style="color: {{ (isset($billingSummary) && ($billingSummary['is_double'] ?? false)) ? '#E11D48' : 'var(--primary-600)' }}; font-weight: 800;">
                  {{ isset($billingSummary) && count($tagihanPending) > 1 ? $billingSummary['total_nominal_formatted'] : (isset($tagihanPending[0]) ? 'Rp ' . number_format($tagihanPending[0]->nominal, 0, ',', '.') : 'Rp 0') }}
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Midtrans Snap QRIS & Transfer simulator -->
          <div id="paymentActionSection" style="margin-top: 16px;">
            @php
              $currentNominal = 75000;
              if (isset($billingSummary) && count($tagihanPending) > 1) {
                  $currentNominal = (int) ($billingSummary['total_nominal'] ?? 75000);
              } elseif (isset($tagihanPending[0])) {
                  $currentNominal = (int) $tagihanPending[0]->nominal;
              }
              $currentNominalFormatted = 'Rp ' . number_format($currentNominal, 0, ',', '.');
              $qrPayload = '00020101021226590014ID.GO.GPN.WWW0118936000020110200391050215000000000000000520459995303360540' . $currentNominal . '5802ID5927KAS RT ' . ($nomorRt ?? '05') . ' HARMONI RESIDENCE6006BOGOR61051611062200716TAG-IURAN-RT056304';
              $initialQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data=' . urlencode($qrPayload);
            @endphp

            <!-- OPSI UTAMA: TAMPILAN MINIMALIS QRIS LANGSUNG & REKENING BCA -->
            <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 18px 16px; margin-bottom: 16px; text-align: center;">
              
              <!-- Direct QRIS Code Box -->
              <div style="display: inline-flex; flex-direction: column; align-items: center; background: #FFFFFF; border: 1px solid #CBD5E1; border-radius: 12px; padding: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 12px;">
                <div style="font-size: 0.6875rem; font-weight: 800; color: #047857; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                  QRIS KAS RT {{ $nomorRt ?? '05' }}
                </div>
                
                <div style="width: 160px; height: 160px; display: flex; align-items: center; justify-content: center; background: #FFFFFF;">
                  <img id="directQrisCodeImage" 
                       src="{{ $initialQrUrl }}" 
                       alt="Scan QRIS Kas RT" 
                       style="width: 160px; height: 160px; object-fit: contain;" 
                       onerror="this.style.display='none'; document.getElementById('directQrisSvgFallback').style.display='block';">
                  
                  <div id="directQrisSvgFallback" style="display: none; width: 160px; height: 160px;">
                    <svg width="160" height="160" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <rect x="5" y="5" width="28" height="28" rx="4" fill="#0F172A"/>
                      <rect x="9" y="9" width="20" height="20" rx="2" fill="#FFFFFF"/>
                      <rect x="13" y="13" width="12" height="12" rx="2" fill="#0F172A"/>
                      <rect x="67" y="5" width="28" height="28" rx="4" fill="#0F172A"/>
                      <rect x="71" y="9" width="20" height="20" rx="2" fill="#FFFFFF"/>
                      <rect x="75" y="13" width="12" height="12" rx="2" fill="#0F172A"/>
                      <rect x="5" y="67" width="28" height="28" rx="4" fill="#0F172A"/>
                      <rect x="9" y="71" width="20" height="20" rx="2" fill="#FFFFFF"/>
                      <rect x="13" y="75" width="12" height="12" rx="2" fill="#0F172A"/>
                      <rect x="38" y="8" width="6" height="6" fill="#0F172A"/>
                      <rect x="48" y="14" width="6" height="6" fill="#0F172A"/>
                      <rect x="56" y="8" width="6" height="6" fill="#0F172A"/>
                      <rect x="38" y="24" width="6" height="6" fill="#0F172A"/>
                      <rect x="48" y="24" width="6" height="6" fill="#0F172A"/>
                      <rect x="8" y="38" width="6" height="6" fill="#0F172A"/>
                      <rect x="20" y="38" width="6" height="6" fill="#0F172A"/>
                      <rect x="40" y="40" width="20" height="20" rx="4" fill="#059669"/>
                      <circle cx="50" cy="50" r="5" fill="#FFFFFF"/>
                      <rect x="68" y="38" width="6" height="6" fill="#0F172A"/>
                      <rect x="84" y="38" width="6" height="6" fill="#0F172A"/>
                      <rect x="38" y="68" width="6" height="6" fill="#0F172A"/>
                      <rect x="48" y="74" width="6" height="6" fill="#0F172A"/>
                      <rect x="58" y="68" width="6" height="6" fill="#0F172A"/>
                      <rect x="68" y="74" width="6" height="6" fill="#0F172A"/>
                      <rect x="78" y="68" width="6" height="6" fill="#0F172A"/>
                      <rect x="86" y="76" width="6" height="6" fill="#0F172A"/>
                      <rect x="68" y="86" width="6" height="6" fill="#0F172A"/>
                      <rect x="80" y="86" width="6" height="6" fill="#0F172A"/>
                    </svg>
                  </div>
                </div>

                <div style="font-size: 0.625rem; font-weight: 800; color: #DC2626; letter-spacing: 0.06em; margin-top: 6px;">
                  QRIS • STANDAR PEMBAYARAN NASIONAL
                </div>
                <button type="button" id="btnCopyMidtransQrUrl" onclick="copyText(window.currentMidtransQrUrl || document.getElementById('directQrisCodeImage').src, this, 'URL Disalin!')" style="margin-top: 6px; font-size: 0.625rem; font-weight: 700; color: #0284C7; background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 6px; padding: 3px 8px; cursor: pointer; transition: all 0.15s;">
                  🔗 Salin URL QR untuk Simulator Midtrans
                </button>
              </div>

              <!-- Minimalist Nominal Display -->
              <div style="font-size: 0.8125rem; color: #475569; margin-bottom: 12px;">
                Total Bayar: <strong id="directQrisNominalText" style="font-size: 1.0625rem; color: #0F172A;">{{ $currentNominalFormatted }}</strong>
              </div>

              <!-- Only 1 Rekening: BCA -->
              <div style="max-width: 320px; margin: 0 auto; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px; text-align: left;">
                  <div style="font-size: 0.75rem; font-weight: 900; background: #E0F2FE; color: #0284C7; padding: 3px 8px; border-radius: 6px;">
                    BCA
                  </div>
                  <div>
                    <div style="font-family: monospace; font-size: 0.875rem; font-weight: 800; color: #0F172A; letter-spacing: 0.03em;">
                      712-889-0192
                    </div>
                    <div style="font-size: 0.6875rem; color: #64748B;">a.n. Kas RT 05 Harmoni</div>
                  </div>
                </div>
                <button type="button" onclick="copyText('7128890192', this, 'Tersalin!')" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 700; background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1; border-radius: 6px; cursor: pointer; transition: all 0.15s; white-space: nowrap;">
                  Salin
                </button>
              </div>

              <!-- Minimalist Test/Simulation Button -->
              <div style="margin-top: 10px;">
                <button type="button" id="btnSimulasiDirectQris" style="padding: 6px 14px; font-size: 0.75rem; font-weight: 700; background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; border-radius: 8px; cursor: pointer; transition: all 0.15s;">
                  ⚡ Uji Simulasi Lunas (Demo)
                </button>
              </div>

              <!-- Hidden elements for test and script compatibility -->
              <button type="button" id="btnBayarMidtransSnap" style="display: none;"><span id="btnSnapText">📱 Scan QRIS</span></button>
              <button type="button" id="btnBukaTransferRekening" style="display: none;"><span>🏦 No. Rekening Bank</span></button>
            </div>


            <!-- PEMBATAS OPSI -->
            <div style="text-align: center; margin: 18px 0; position: relative;">
              <hr style="border: 0; border-top: 1px solid #E2E8F0; margin: 0;">
              <span style="position: absolute; top: -9px; left: 50%; transform: translateX(-50%); background: #FFFFFF; padding: 0 12px; font-size: 0.6875rem; color: #94A3B8; font-weight: 700; text-transform: uppercase;">
                Atau Opsi Unggah Struk Manual
              </span>
            </div>

            <!-- OPSI 2: ACCORDION TRANSFER MANUAL & UPLOAD BUKTI -->
            <details style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px 16px;">
              <summary style="font-size: 0.8125rem; font-weight: 700; color: #334155; cursor: pointer; user-select: none;">
                Unggah File Struk / Bukti Transfer Manual ke Bendahara
              </summary>
              <div style="margin-top: 14px; border-top: 1px dashed #CBD5E1; padding-top: 12px;">
                <label class="form-label" style="font-weight: 700;">Unggah File Struk / Bukti Transfer <span class="req">*</span></label>
                <input type="file" class="form-input" id="fileBuktiBayar" name="bukti_bayar" accept="image/*,application/pdf">
                <div class="form-hint">Mendukung format gambar (JPG, PNG) atau PDF maksimal 5MB.</div>
                
                <button type="submit" class="btn btn-secondary" id="btnKonfirmasiBayar" style="width: 100%; margin-top: 12px; font-weight: 700; background: #475569; color: #FFFFFF; border: none; padding: 10px; border-radius: 8px; cursor: pointer;">
                  Unggah Bukti & Minta Verifikasi Bendahara
                </button>
              </div>
            </details>
          </div>
        </div>
      </div>

      <div class="modal-footer" style="flex-shrink: 0; padding: 14px 24px; background: #F8FAFC; border-top: 1px solid #E2E8F0;">
        <button type="button" class="btn btn-subtle" data-close-modal>Tutup</button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: POPUP PAYMENT GATEWAY (QRIS SCAN & NOMOR REKENING BANK)
     ========================================================================== -->
<div class="modal-overlay" id="modalQrisPopup" role="dialog" aria-modal="true" style="z-index: 10000;">
  <div class="modal-container" style="max-width: 480px; max-height: 90vh; display: flex; flex-direction: column; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
    <!-- Modal Header -->
    <div class="modal-header" style="background: linear-gradient(135deg, #065F46 0%, #047857 100%); color: #FFFFFF; padding: 16px 20px; flex-shrink: 0;">
      <div class="modal-title-group">
        <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem;">
          💳
        </div>
        <div>
          <h3 class="modal-title" style="color: #FFFFFF; font-size: 1.125rem; margin: 0; font-weight: 800;">Pembayaran Kas RT 05</h3>
          <div class="modal-subtitle" style="color: #D1FAE5; font-size: 0.75rem;">Scan QRIS Otomatis atau Transfer Nomor Rekening</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal style="color: #FFFFFF; font-size: 1.5rem;">&times;</button>
    </div>

    <!-- Segmented Tab Header: QRIS vs No Rekening -->
    <div style="display: flex; background: #F1F5F9; padding: 6px; gap: 6px; border-bottom: 1px solid #E2E8F0; flex-shrink: 0;">
      <button type="button" id="tabBtnQris" class="payment-method-tab" onclick="switchPaymentMethodTab('qris')" style="flex: 1; padding: 10px 12px; font-size: 0.8125rem; font-weight: 800; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; background: #FFFFFF; color: #047857; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="18" height="18" x="3" y="3" rx="2"/><rect width="4" height="4" x="7" y="7"/><rect width="4" height="4" x="13" y="7"/><rect width="4" height="4" x="7" y="13"/><rect width="4" height="4" x="13" y="13"/></svg>
        <span>📱 Scan QRIS</span>
      </button>

      <button type="button" id="tabBtnBank" class="payment-method-tab" onclick="switchPaymentMethodTab('bank')" style="flex: 1; padding: 10px 12px; font-size: 0.8125rem; font-weight: 700; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s; background: transparent; color: #64748B;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
        <span>🏦 No. Rekening Bank</span>
      </button>
    </div>

    <!-- Modal Body: Scrollable -->
    <div class="modal-body" style="padding: 20px; overflow-y: auto; flex: 1; min-height: 0; background: #FFFFFF;">
      
      <!-- ==================== TAB 1: SCAN QRIS ==================== -->
      <div id="tabPaneQris" style="display: block; text-align: center;">
        <!-- Merchant Info -->
        <div style="font-size: 0.75rem; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 0.06em;">Merchant Resmi Terdaftar</div>
        <div style="font-size: 1.125rem; font-weight: 900; color: #0F172A; margin: 2px 0;">KAS RT 05 HARMONI RESIDENCE</div>
        <div style="font-size: 0.6875rem; color: #64748B; margin-bottom: 12px;">NMID: <code style="font-weight: 700; color: #334155;">ID10202688991</code> • Interoperable QRIS GPN</div>

        <!-- Live Real-Time Auto-Detection Badge -->
        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 4px 12px; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; color: #047857; margin-bottom: 12px;">
          <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
          <span>⚡ Langsung Lunas Otomatis Begitu Selesai di-Scan</span>
        </div>

        <!-- QR Code Visual Card -->
        <div style="margin: 0 auto 14px auto; width: 220px; padding: 12px; background: #FFFFFF; border: 2px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.06); display: flex; flex-direction: column; align-items: center; justify-content: center;">
          <!-- Dynamic High-Res QR Code Image from API with SVG Fallback -->
          <img id="qrisImageCode" src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&amp;margin=8&amp;data=https%3A%2F%2Fkas-rt05.test%2Fbayar%2Fiuran%3Famount%3D75000" alt="Scan QRIS Kas RT" style="width: 180px; height: 180px; object-fit: contain; border-radius: 6px;" onerror="this.style.display='none'; document.getElementById('qrisSvgFallback').style.display='block';">

          <!-- Crisp SVG QR Code Fallback (for offline environments) -->
          <div id="qrisSvgFallback" style="display: none; width: 180px; height: 180px;">
            <svg width="180" height="180" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="5" y="5" width="28" height="28" rx="4" fill="#0F172A"/>
              <rect x="9" y="9" width="20" height="20" rx="2" fill="#FFFFFF"/>
              <rect x="13" y="13" width="12" height="12" rx="2" fill="#0F172A"/>
              
              <rect x="67" y="5" width="28" height="28" rx="4" fill="#0F172A"/>
              <rect x="71" y="9" width="20" height="20" rx="2" fill="#FFFFFF"/>
              <rect x="75" y="13" width="12" height="12" rx="2" fill="#0F172A"/>
              
              <rect x="5" y="67" width="28" height="28" rx="4" fill="#0F172A"/>
              <rect x="9" y="71" width="20" height="20" rx="2" fill="#FFFFFF"/>
              <rect x="13" y="75" width="12" height="12" rx="2" fill="#0F172A"/>

              <rect x="38" y="8" width="6" height="6" fill="#0F172A"/>
              <rect x="48" y="14" width="6" height="6" fill="#0F172A"/>
              <rect x="56" y="8" width="6" height="6" fill="#0F172A"/>
              <rect x="38" y="24" width="6" height="6" fill="#0F172A"/>
              <rect x="48" y="24" width="6" height="6" fill="#0F172A"/>
              
              <rect x="8" y="38" width="6" height="6" fill="#0F172A"/>
              <rect x="20" y="38" width="6" height="6" fill="#0F172A"/>
              <rect x="40" y="40" width="20" height="20" rx="4" fill="#059669"/>
              <circle cx="50" cy="50" r="5" fill="#FFFFFF"/>
              <rect x="68" y="38" width="6" height="6" fill="#0F172A"/>
              <rect x="84" y="38" width="6" height="6" fill="#0F172A"/>

              <rect x="38" y="68" width="6" height="6" fill="#0F172A"/>
              <rect x="48" y="74" width="6" height="6" fill="#0F172A"/>
              <rect x="58" y="68" width="6" height="6" fill="#0F172A"/>
              <rect x="68" y="74" width="6" height="6" fill="#0F172A"/>
              <rect x="78" y="68" width="6" height="6" fill="#0F172A"/>
              <rect x="86" y="76" width="6" height="6" fill="#0F172A"/>
              <rect x="68" y="86" width="6" height="6" fill="#0F172A"/>
              <rect x="80" y="86" width="6" height="6" fill="#0F172A"/>
            </svg>
          </div>

          <div style="margin-top: 6px; font-size: 0.625rem; font-weight: 800; color: #DC2626; letter-spacing: 0.08em; text-transform: uppercase;">
            QRIS - STANDAR NASIONAL
          </div>
        </div>

        <!-- Total Tagihan Badge -->
        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px; margin-bottom: 12px;">
          <div style="font-size: 0.75rem; color: #64748B;">Total Tagihan yang Harus Dibayar:</div>
          <div id="qrisModalTotalAmount" style="font-size: 1.5rem; font-weight: 900; color: #059669; margin: 2px 0;">
            Rp 75.000
          </div>
          <div id="qrisModalOrderId" style="font-size: 0.6875rem; color: #94A3B8; font-family: monospace;">
            Order ID: IURAN-RT05-PENDING
          </div>
        </div>

        <!-- Fitur Khusus: Midtrans Payment Simulator Integration -->
        <div style="background: #F0FDF4; border: 1.5px dashed #22C55E; border-radius: 12px; padding: 12px; margin-bottom: 14px; text-align: left;">
          <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
            <strong style="font-size: 0.8125rem; color: #15803D; display: flex; align-items: center; gap: 6px;">
              <span>🧪 Midtrans Sandbox Simulator</span>
            </strong>
            <a href="https://simulator.sandbox.midtrans.com/v2/qris/index" target="_blank" style="font-size: 0.6875rem; color: #15803D; font-weight: 700; text-decoration: underline;">
              Buka Simulator ↗
            </a>
          </div>
          <div style="font-size: 0.6875rem; color: #166534; margin-bottom: 6px; line-height: 1.4;">
            Salin <strong>QR Code Image Url</strong> di bawah lalu paste ke kolom simulator:
          </div>
          <div style="display: flex; gap: 6px;">
            <input type="text" id="qrisCodeImageUrlInput" readonly style="flex: 1; padding: 6px 10px; font-size: 0.6875rem; font-family: monospace; background: #FFFFFF; border: 1px solid #86EFAC; border-radius: 6px; color: #334155;" value="https://api.sandbox.midtrans.com/v2/qris/demo-75000/qr-code">
            <button type="button" onclick="copyText(document.getElementById('qrisCodeImageUrlInput').value, this, 'Disalin!')" style="padding: 6px 12px; font-size: 0.6875rem; font-weight: 700; background: #16A34A; color: #FFFFFF; border: none; border-radius: 6px; cursor: pointer; white-space: nowrap;">
              📋 Salin URL
            </button>
          </div>
        </div>

        <!-- Real-time Status Card (Tanpa Tombol Manual) -->
        <div style="background: linear-gradient(135deg, #F0FDF4 0%, #ECFDF5 100%); border: 1.5px solid #86EFAC; border-radius: 12px; padding: 14px 16px; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);">
          <div style="width: 10px; height: 10px; border-radius: 50%; background: #10B981; animation: pulse 1.5s infinite; flex-shrink: 0;"></div>
          <span style="font-size: 0.8125rem; font-weight: 800; color: #065F46;">
            Menunggu Scan QRIS... (Otomatis Lunas Seketika)
          </span>
        </div>
      </div>

      <!-- ==================== TAB 2: TRANSFER NOMOR REKENING ==================== -->
      <div id="tabPaneBank" style="display: none;">
        <!-- Nominal Tagihan Ringkas -->
        <div style="background: linear-gradient(135deg, #EFF6FF 0%, #DBEAFE 100%); border: 1px solid #BFDBFE; border-radius: 12px; padding: 12px 14px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center;">
          <div>
            <div style="font-size: 0.6875rem; font-weight: 700; color: #1E40AF; text-transform: uppercase;">Total yang Harus Ditransfer</div>
            <div id="bankModalTotalAmount" style="font-size: 1.25rem; font-weight: 900; color: #1D4ED8;">Rp 75.000</div>
          </div>
          <button type="button" class="btn-copy-nominal" onclick="copyText('75000', this, 'Tersalin!')" style="padding: 6px 12px; font-size: 0.75rem; font-weight: 700; background: #FFFFFF; color: #1D4ED8; border: 1px solid #93C5FD; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 4px;">
            <span>📋 Salin Nominal</span>
          </button>
        </div>

        <div style="font-size: 0.8125rem; font-weight: 800; color: #0F172A; margin-bottom: 8px;">
          Pilih Salah Satu Rekening Bank Kas RT:
        </div>

        <!-- Daftar Kartu Rekening Bank -->
        <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px;">
          <!-- 1. BCA -->
          <div style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div>
              <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                <span style="font-weight: 900; font-size: 0.8125rem; color: #00569B; background: #E0F2FE; padding: 2px 6px; border-radius: 4px;">BCA</span>
                <span style="font-size: 0.75rem; color: #64748B;">Bank Central Asia</span>
              </div>
              <div style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; font-family: monospace; letter-spacing: 0.05em;">712-889-0192</div>
              <div style="font-size: 0.6875rem; color: #64748B;">a.n. PENGURUS RT 05 HARMONI RESIDENCE</div>
            </div>
            <button type="button" onclick="copyText('7128890192', this, 'Tersalin!')" style="padding: 8px 12px; font-size: 0.75rem; font-weight: 700; background: #F8FAFC; color: #334155; border: 1px solid #CBD5E1; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
              <span>📋 Salin</span>
            </button>
          </div>

          <!-- 2. Mandiri -->
          <div style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div>
              <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                <span style="font-weight: 900; font-size: 0.8125rem; color: #034EA2; background: #FEF3C7; padding: 2px 6px; border-radius: 4px;">MANDIRI</span>
                <span style="font-size: 0.75rem; color: #64748B;">Bank Mandiri</span>
              </div>
              <div style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; font-family: monospace; letter-spacing: 0.05em;">132-00-1928374-1</div>
              <div style="font-size: 0.6875rem; color: #64748B;">a.n. KAS RT 05 HARMONI RESIDENCE</div>
            </div>
            <button type="button" onclick="copyText('1320019283741', this, 'Tersalin!')" style="padding: 8px 12px; font-size: 0.75rem; font-weight: 700; background: #F8FAFC; color: #334155; border: 1px solid #CBD5E1; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
              <span>📋 Salin</span>
            </button>
          </div>

          <!-- 3. BRI -->
          <div style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 12px 14px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
            <div>
              <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 2px;">
                <span style="font-weight: 900; font-size: 0.8125rem; color: #00529C; background: #EFF6FF; padding: 2px 6px; border-radius: 4px;">BRI</span>
                <span style="font-size: 0.75rem; color: #64748B;">Bank Rakyat Indonesia</span>
              </div>
              <div style="font-size: 1.0625rem; font-weight: 800; color: #0F172A; font-family: monospace; letter-spacing: 0.05em;">0341-01-002847-50-3</div>
              <div style="font-size: 0.6875rem; color: #64748B;">a.n. KAS RT 05 HARMONI RESIDENCE</div>
            </div>
            <button type="button" onclick="copyText('034101002847503', this, 'Tersalin!')" style="padding: 8px 12px; font-size: 0.75rem; font-weight: 700; background: #F8FAFC; color: #334155; border: 1px solid #CBD5E1; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
              <span>📋 Salin</span>
            </button>
          </div>
        </div>

        <!-- Instruksi Singkat -->
        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 10px 12px; font-size: 0.75rem; color: #475569; margin-bottom: 14px; line-height: 1.5;">
          <strong>💡 Langkah Pembayaran Transfer:</strong>
          <ol style="margin: 4px 0 0 16px; padding: 0;">
            <li>Salin salah satu nomor rekening dan nominal di atas.</li>
            <li>Lakukan transfer via m-Banking atau ATM ke rekening kas RT.</li>
            <li>Klik tombol di bawah ini untuk konfirmasi otomatis atau simpan bukti transfer.</li>
          </ol>
        </div>

        <!-- Tombol Konfirmasi Selesai Transfer -->
        <button type="button" id="btnExecuteBankTransferSimulation" style="width: 100%; padding: 13px; font-size: 0.9375rem; font-weight: 800; background: #0F172A; color: #FFFFFF; border: none; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
          <span>⚡ Konfirmasi Sudah Transfer (Verifikasi Kas)</span>
        </button>
      </div>

    </div>

    <!-- Modal Footer -->
    <div class="modal-footer" style="padding: 12px 20px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
      <span style="font-size: 0.6875rem; color: #64748B;">Batas Waktu Transaksi: <strong id="paymentTimerCountdown">14:59</strong></span>
      <button type="button" class="btn btn-subtle" data-close-modal style="padding: 6px 14px; font-size: 0.8125rem;">Tutup</button>
    </div>
  </div>
</div>

<!-- ==========================================================================
     MODAL: PINJAM INVENTARIS RT (Catalog view of INVENTARIS with stock status)
     ========================================================================== -->
<div class="modal-overlay" id="modalPinjam" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 760px;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--purple-50); color: var(--purple-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Pinjam Inventaris RT</h3>
          <div class="modal-subtitle">Katalog lengkap aset bersama balai warga dengan status ketersediaan stok</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formPinjamAset" action="{{ route('portal.pinjam-aset') }}" method="POST">
      @csrf
      <input type="hidden" id="selectedAsetNama" name="aset" value="{{ isset($inventarisCatalog) && count($inventarisCatalog) > 0 ? $inventarisCatalog[0]['nama'] : 'Tenda Lipat (3x6 Meter)' }}">

      <div class="modal-body">
        <div class="form-group">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <label class="form-label" style="font-weight: 700; margin: 0;">Katalog Inventaris RT & Status Stok <span class="req">*</span></label>
            <span class="badge badge-slate" style="font-size: 0.6875rem;">Pilih salah satu alat</span>
          </div>

          <!-- Catalog View Grid of INVENTARIS -->
          <div class="inventaris-catalog-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 10px; max-height: 250px; overflow-y: auto; padding: 6px; border: 1px solid var(--slate-200); border-radius: var(--radius-md); background-color: var(--slate-50);">
            @forelse($inventarisCatalog ?? [] as $item)
              <label class="catalog-item-card" style="border: 1px solid var(--slate-200); border-radius: var(--radius-sm); padding: 12px; background: #ffffff; cursor: pointer; display: flex; flex-direction: column; gap: 6px; transition: all var(--transition-fast); box-shadow: var(--shadow-xs);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                  <input type="radio" name="inventaris_id" value="{{ $item['id'] }}" data-nama="{{ $item['nama'] }}" {{ $loop->first ? 'checked' : '' }} onchange="document.getElementById('selectedAsetNama').value = this.dataset.nama;">
                  <span class="badge {{ $item['status_badge'] }}" style="font-size: 0.6875rem;">{{ $item['status_label'] }}</span>
                </div>
                <strong style="font-size: 0.8125rem; color: var(--slate-900); line-height: 1.3;">{{ $item['nama'] }}</strong>
                <div style="font-size: 0.6875rem; color: var(--slate-500); display: flex; justify-content: space-between;">
                  <span>Kode: <code>{{ $item['kode'] }}</code></span>
                  <span>Stok: <strong>{{ $item['jumlah'] }}</strong></span>
                </div>
                <div style="font-size: 0.6875rem; color: var(--slate-400);">📍 {{ $item['lokasi'] }}</div>
              </label>
            @empty
              <div style="padding: 20px; text-align: center; color: var(--slate-400); grid-column: 1 / -1;">
                Data inventaris belum tersedia di sistem.
              </div>
            @endforelse
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Tanggal Mulai Pakai <span class="req">*</span></label>
            <input type="date" class="form-input" id="tglMulaiPinjam" name="tgl_mulai" required>
          </div>
          <div class="form-group">
            <label class="form-label">Tanggal Pengembalian <span class="req">*</span></label>
            <input type="date" class="form-input" id="tglSelesaiPinjam" name="tgl_selesai" required>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Nama Peminjam <span class="req">*</span></label>
            <input type="text" class="form-input" id="namaPeminjam" name="nama" value="{{ $activeCitizen->nama ?? '' }}" placeholder="Nama Warga" required>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Rumah / Blok <span class="req">*</span></label>
            <input type="text" class="form-input" id="blokPeminjam" name="blok" value="{{ $activeCitizen->alamat ?? '' }}" placeholder="Contoh: A-03" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Tujuan Pemakaian <span class="req">*</span></label>
          <input type="text" class="form-input" id="tujuanPinjam" name="tujuan" placeholder="Misal: Acara syukuran aqiqah / Arisan warga / Duka cita..." required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" style="background-color: var(--purple-600);" id="btnSubmitPinjam">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Ajukan Peminjaman Inventaris
        </button>
      </div>
    </form>
  </div>
</div>


<!-- ==========================================================================
     MODAL: SAMPAIKAN ASPIRASI (Submits to ASPIRASI table)
     ========================================================================== -->
<div class="modal-overlay" id="modalAspirasi" role="dialog" aria-modal="true">
  <div class="modal-container">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--amber-50); color: var(--amber-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Sampaikan Aspirasi</h3>
          <div class="modal-subtitle">Sampaikan saran, perbaikan lingkungan, atau kritik langsung ke tabel ASPIRASI</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formKirimAspirasi" action="{{ route('portal.submit-aspirasi') }}" method="POST">
      @csrf
      <input type="hidden" name="nik" id="aspirasiNik" value="{{ $activeCitizen->nik ?? '3201051208940001' }}">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Kategori Masukan / Aduan <span class="req">*</span></label>
          <select class="form-select" id="kategoriAspirasi" name="kategori" required>
            <option value="fasilitas">Fasilitas Umum (Jalan, Lampu, Selokan)</option>
            <option value="keamanan">Keamanan & Ronda Lingkungan</option>
            <option value="kebersihan">Kebersihan & Pengangkutan Sampah</option>
            <option value="kegiatan">Kegiatan Sosial & Keakraban Warga</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Judul Aspirasi / Pokok Bahasan <span class="req">*</span></label>
          <input type="text" class="form-input" id="judulAspirasi" name="judul" placeholder="Misal: Penambahan Tong Sampah di Taman Bermain" required>
        </div>

        <div class="form-group">
          <label class="form-label">Detail Aspirasi / Uraian Masalah <span class="req">*</span></label>
          <textarea class="form-textarea" id="isiAspirasi" name="isi" placeholder="Jelaskan lokasi detail, kronologi, atau usulan solusi Anda..." required style="min-height: 110px;"></textarea>
        </div>

        <div class="form-group">
          <label class="radio-card-label" style="display: flex; align-items: center; justify-content: flex-start; cursor: pointer;">
            <input type="checkbox" id="anonimCheckbox" name="is_anonim" value="1" style="width: 18px; height: 18px;">
            <span class="radio-card-text" style="font-weight: 600; margin-left: 8px;">Kirim Secara Anonim (Kerahasiaan nama terjaga di feed umum)</span>
          </label>
        </div>

        <div id="identitasAspirasiGroup" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Nama Pengirim</label>
            <input type="text" class="form-input" id="namaAspirasi" name="nama" value="{{ $activeCitizen->nama ?? '' }}" placeholder="Nama Lengkap">
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Rumah / Blok</label>
            <input type="text" class="form-input" id="blokAspirasi" name="blok" value="{{ $activeCitizen->alamat ?? '' }}" placeholder="Misal: Blok C-10">
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitAspirasi" style="background-color: var(--amber-600);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
          Kirim Aspirasi Warga
        </button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: USULAN KEGIATAN WARGA
     ========================================================================== -->
<div class="modal-overlay" id="modalUsulanKegiatan" role="dialog" aria-modal="true">
  <div class="modal-container">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Ajukan Usulan Kegiatan Warga</h3>
          <div class="modal-subtitle">Gagas kegiatan positif untuk mempererat tali silaturahmi</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formUsulKegiatan" action="{{ route('portal.usul-kegiatan') }}" method="POST">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nama Kegiatan yang Diusulkan <span class="req">*</span></label>
          <input type="text" class="form-input" id="namaKegiatanUsul" name="nama_kegiatan" placeholder="Contoh: Turnamen Bulutangkis Antar Gang HUT RI" required>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Perkiraan Waktu Pelaksanaan <span class="req">*</span></label>
            <input type="text" class="form-input" id="waktuKegiatanUsul" name="waktu" placeholder="Misal: Minggu pagi pertengahan bulan depan" required>
          </div>
          <div class="form-group">
            <label class="form-label">Perkiraan Lokasi <span class="req">*</span></label>
            <input type="text" class="form-input" id="lokasiKegiatanUsul" name="lokasi" placeholder="Balai Warga / Lapangan Blok A" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Deskripsi & Rencana Partisipan <span class="req">*</span></label>
          <textarea class="form-textarea" id="deskripsiKegiatanUsul" name="deskripsi" placeholder="Jelaskan tujuan kegiatan, siapa saja yang dilibatkan, dan kebutuhan pendukung..." required></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
          <div class="form-group">
            <label class="form-label">Nama Pengusul <span class="req">*</span></label>
            <input type="text" class="form-input" id="pengusulKegiatan" name="pengusul" placeholder="Nama Warga" required>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor WhatsApp Pengusul <span class="req">*</span></label>
            <input type="tel" class="form-input" id="waPengusul" name="no_wa" placeholder="08xxxxxxxxxx" required>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary">Kirim Usulan ke Pengurus RT</button>
      </div>
    </form>
  </div>
</div>

<!-- ==========================================================================
     MODAL: MASUK AKUN WARGA
     ========================================================================== -->
<div class="modal-overlay" id="modalLoginWarga" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 440px;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
          <h3 class="modal-title">Masuk Akun Warga</h3>
          <div class="modal-subtitle">Akses riwayat pengajuan surat & bukti iuran</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formLoginWarga">
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nomor NIK atau No. WhatsApp Terdaftar</label>
          <input type="text" class="form-input" id="loginIdentifier" placeholder="Contoh: 3201xxxxxxxxxxxx atau 0812345678" required>
        </div>

        <div class="form-group">
          <label class="form-label">PIN Akun Warga (6 Digit)</label>
          <input type="password" class="form-input" id="loginPin" placeholder="******" maxlength="6" required>
          <div class="form-hint">PIN diberikan saat pendataan warga oleh Sekretariat RT.</div>
        </div>

        <div style="font-size: 0.8125rem; color: var(--slate-500); line-height: 1.5;">
          💡 <em>Belum memiliki PIN atau baru pindah ke lingkungan RT 05? Hubungi Sekretaris RT melalui tombol kontak di bagian bawah halaman.</em>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk ke Profil Warga</button>
      </div>
    </form>
  </div>
</div>


<!-- ==========================================================================
     MODAL: CEK STATUS SURAT & MUTASI IURAN MANDIRI WARGA
     ========================================================================== -->
<div class="modal-overlay" id="modalStatusWarga" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 780px;">
    <!-- Modal Header -->
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" x2="8" y1="13" y2="13"/>
            <line x1="16" x2="8" y1="17" y2="17"/>
            <line x1="10" x2="8" y1="9" y2="9"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Layanan Riwayat & Status Warga</h3>
          <div class="modal-subtitle">Pantau status ACC surat pengantar RT & riwayat mutasi iuran bulanan keluarga</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <!-- Modal Body -->
    <div class="modal-body" style="padding: 20px 24px;">
      <!-- Quick Search & Citizen Switcher Filter Bar -->
      <div style="background-color: var(--slate-50); border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 14px 16px; margin-bottom: 20px;">
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <div style="font-size: 0.75rem; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.5px;">
            Cari Akun Warga atau No. Rumah Lain:
          </div>
          <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
              <input 
                type="text" 
                id="searchWargaQueryInput" 
                class="form-input" 
                placeholder="Ketik NIK 16 digit, No. Blok (misal: B-12), atau nama..." 
                style="padding: 8px 14px; font-size: 0.8125rem; background-color: #ffffff;"
              >
            </div>
            <button type="button" class="btn btn-primary" id="btnSearchWargaRiwayat" style="padding: 8px 18px; font-size: 0.8125rem;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
              </svg>
              <span>Cari Data</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Active Citizen Profile Card -->
      <div style="background-color: #ffffff; border: 1px solid var(--slate-200); border-radius: var(--radius-lg); padding: 16px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px; box-shadow: var(--shadow-xs);">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div class="citizen-avatar" id="modalCitizenAvatar" style="width: 44px; height: 44px; font-size: 1rem;">
              {{ strtoupper(substr($activeCitizen->nama ?? 'HW', 0, 2)) }}
            </div>
            <div>
              <div style="display: flex; align-items: center; gap: 6px;">
                <h4 style="font-size: 1rem; font-weight: 800; color: var(--slate-900); line-height: 1.2;" id="modalCitizenName">
                  {{ $activeCitizen->nama ?? 'Bpk. Hendra Wijaya' }}
                </h4>
                <span class="badge badge-blue" style="font-size: 0.6875rem;" id="modalCitizenDomisili">
                  {{ ucfirst($activeCitizen->status_domisili ?? 'Tetap') }}
                </span>
              </div>
              <p style="font-size: 0.75rem; color: var(--slate-500); margin-top: 2px;" id="modalCitizenAddress">
                {{ $activeCitizen->alamat ?? 'Blok B No. 12' }} • No. KK: {{ $activeCitizen->no_kk ?? '3201051909120008' }} • NIK: {{ $activeCitizen->nik ?? '3201051208940001' }}
              </p>
            </div>
          </div>

          <div style="display: flex; align-items: center; gap: 8px;">
            <span class="badge badge-emerald" id="modalSummarySuratBadge" style="font-size: 0.75rem; padding: 4px 10px;">
              {{ $citizenRiwayat['summary']['total_surat_acc'] ?? 0 }} Surat Di-ACC
            </span>
            <span class="badge {{ ($citizenRiwayat['summary']['is_lunas_bulan_ini'] ?? false) ? 'badge-emerald' : 'badge-amber' }}" id="modalSummaryIuranBadge" style="font-size: 0.75rem; padding: 4px 10px;">
              {{ ($citizenRiwayat['summary']['is_lunas_bulan_ini'] ?? false) ? 'Iuran Sep: Lunas' : 'Iuran Sep: Belum Lunas' }}
            </span>
          </div>
        </div>
      </div>

      <!-- Tabs Navigation -->
      <div class="warga-tab-nav">
        <button type="button" class="warga-tab-btn active" data-tab-target="tabSuratWarga" id="btnTabSurat">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" x2="8" y1="13" y2="13"/>
            <line x1="16" x2="8" y1="17" y2="17"/>
          </svg>
          <span>Status Pengajuan Surat (Cek ACC)</span>
          <span class="badge badge-emerald" id="tabSuratCountBadge" style="font-size: 0.6875rem;">
            {{ count($citizenRiwayat['letters'] ?? []) }}
          </span>
        </button>

        <button type="button" class="warga-tab-btn" data-tab-target="tabMutasiWarga" id="btnTabMutasi">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" x2="12" y1="2" y2="22"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
          <span>Mutasi Iuran Kas Keluarga</span>
          <span class="badge badge-blue" id="tabMutasiCountBadge" style="font-size: 0.6875rem;">
            {{ count($citizenRiwayat['mutations'] ?? []) }}
          </span>
        </button>
      </div>

      <!-- ====================================================================
           TAB CONTENT 1: STATUS SURAT PENGANTAR (ACC / PENDING / REJECTED)
           ==================================================================== -->
      <div class="warga-tab-content active" id="tabSuratWarga">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
          <div>
            <h4 style="font-size: 0.875rem; font-weight: 800; color: var(--slate-900);">Daftar Surat Pengantar & Resi Permohonan</h4>
            <p style="font-size: 0.75rem; color: var(--slate-500);">Dokumen yang sudah di-ACC Ketua RT dapat langsung diunduh salinan PDF aslinya.</p>
          </div>
          <button type="button" class="btn btn-primary" data-modal="modalSurat" style="font-size: 0.75rem; padding: 6px 12px;">
            + Ajukan Surat Baru
          </button>
        </div>

        <div class="surat-card-list" id="suratCardsListWrapper">
          @forelse($citizenRiwayat['letters'] ?? [] as $let)
            <div class="surat-card-item {{ $let['is_acc'] ? 'acc-approved' : ($let['is_rejected'] ? 'acc-rejected' : 'acc-pending') }}">
              <div class="surat-header-row">
                <div>
                  <div class="surat-title-bold">{{ $let['jenis_surat'] }}</div>
                  <div class="surat-number-tag">
                    No: {{ $let['nomor_surat'] }}
                  </div>
                </div>

                <div>
                  @if($let['is_acc'])
                    <span class="badge badge-emerald" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                      <span>SUDAH DI-ACC KETUA RT</span>
                    </span>
                  @elseif($let['is_rejected'])
                    <span class="badge badge-rose" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px;">
                      DITOLAK / REVISI
                    </span>
                  @else
                    <span class="badge badge-amber" style="font-size: 0.75rem; font-weight: 800; padding: 4px 10px; display: inline-flex; align-items: center; gap: 4px;">
                      <span style="width: 6px; height: 6px; border-radius: 50%; background-color: var(--amber-500); animation: pulse 1.5s infinite;"></span>
                      <span>MENUNGGU PERSETUJUAN</span>
                    </span>
                  @endif
                </div>
              </div>

              <div class="surat-desc-text">
                <strong>Keperluan:</strong> {{ $let['keperluan'] }}
              </div>

              <div style="font-size: 0.75rem; color: var(--slate-500); background-color: var(--slate-50); padding: 8px 12px; border-radius: var(--radius-sm); border: 1px dashed var(--slate-200);">
                💡 <em>{{ $let['keterangan_acc'] }}</em>
              </div>

              <div class="surat-action-footer">
                <div style="font-size: 0.75rem; color: var(--slate-400);">
                  Diajukan: <strong>{{ $let['tanggal'] }}</strong>
                </div>

                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                  @if($let['is_acc'])
                    <a href="{{ $let['pdf_url'] }}" target="_blank" class="btn btn-primary" style="font-size: 0.75rem; padding: 6px 12px; gap: 6px; text-decoration: none;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      <span>Unduh PDF Ber-QR Code</span>
                    </a>
                    <a href="{{ $let['verify_url'] }}" target="_blank" class="btn btn-subtle" style="font-size: 0.75rem; padding: 6px 12px; text-decoration: none;">
                      Verifikasi Digital
                    </a>
                  @else
                    <span style="font-size: 0.75rem; color: var(--amber-700); font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 14 14"/>
                      </svg>
                      <span>Menunggu Tanda Tangan Pak RT</span>
                    </span>
                  @endif
                </div>
              </div>
            </div>
          @empty
            <div style="text-align: center; padding: 36px 16px; background-color: var(--slate-50); border: 1px dashed var(--slate-300); border-radius: var(--radius-lg); color: var(--slate-500); font-size: 0.8125rem;">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--slate-400); margin: 0 auto 8px auto;">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
              </svg>
              <div>Belum ada arsip permohonan surat untuk akun ini.</div>
              <button type="button" class="btn btn-primary" data-modal="modalSurat" style="margin-top: 12px; font-size: 0.75rem;">
                + Buat Permohonan Surat Sekarang
              </button>
            </div>
          @endforelse
        </div>
      </div>

      <!-- ====================================================================
           TAB CONTENT 2: BUKU MUTASI IURAN MANDIRI
           ==================================================================== -->
      <div class="warga-tab-content" id="tabMutasiWarga">
        <!-- Summary Cards for Household Dues -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 18px;">
          <div style="background-color: var(--emerald-50); border: 1px solid var(--emerald-100); border-radius: var(--radius-md); padding: 14px 16px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--emerald-800); text-transform: uppercase;">Tagihan September 2026:</span>
            <div style="font-size: 1.125rem; font-weight: 800; color: var(--emerald-700); margin-top: 4px;" id="modalIuranStatusText">
              {{ ($citizenRiwayat['summary']['is_lunas_bulan_ini'] ?? false) ? 'LUNAS (Rp 75.000)' : 'BELUM DIBAYAR' }}
            </div>
            <span style="font-size: 0.6875rem; color: var(--emerald-600);">Keamanan & Sampah Lingkungan RT 05</span>
          </div>

          <div style="background-color: var(--primary-50); border: 1px solid var(--primary-100); border-radius: var(--radius-md); padding: 14px 16px;">
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary-800); text-transform: uppercase;">Total Iuran Terbayar 2026:</span>
            <div style="font-size: 1.125rem; font-weight: 800; color: var(--primary-700); margin-top: 4px;" id="modalTotalIuranText">
              {{ $citizenRiwayat['summary']['total_terbayar_tahun_ini'] ?? 'Rp 0' }}
            </div>
            <span style="font-size: 0.6875rem; color: var(--primary-600);">Tercatat sah di pembukuan kas bendahara</span>
          </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
          <div>
            <h4 style="font-size: 0.875rem; font-weight: 800; color: var(--slate-900);">Riwayat Mutasi Pembayaran Iuran Keluarga</h4>
            <p style="font-size: 0.75rem; color: var(--slate-500);">Daftar setoran kas bulanan terverifikasi via QRIS dan transfer perbankan.</p>
          </div>
          <button type="button" class="btn btn-success" data-modal="modalIuran" style="font-size: 0.75rem; padding: 6px 12px;">
            + Bayar Iuran / Konfirmasi
          </button>
        </div>

        <!-- Table Container -->
        <div style="overflow-x: auto; border: 1px solid var(--slate-200); border-radius: var(--radius-lg); background-color: #ffffff;">
          <table class="mutasi-table">
            <thead>
              <tr>
                <th>Periode</th>
                <th>Tanggal</th>
                <th>Nominal</th>
                <th>Kategori & Metode</th>
                <th>Status Verifikasi</th>
                <th style="text-align: right;">Bukti</th>
              </tr>
            </thead>
            <tbody id="mutasiTableBody">
              @forelse($citizenRiwayat['mutations'] ?? [] as $mut)
                <tr>
                  <td>
                    <strong style="color: var(--slate-900); font-size: 0.8125rem;">{{ $mut['periode'] }}</strong>
                    <div style="font-size: 0.6875rem; color: var(--slate-400); font-family: monospace;">{{ $mut['ref_code'] }}</div>
                  </td>
                  <td style="color: var(--slate-600); white-space: nowrap;">
                    {{ $mut['tanggal'] }}
                  </td>
                  <td>
                    <strong style="color: var(--emerald-600); font-weight: 800;">{{ $mut['nominal_formatted'] }}</strong>
                  </td>
                  <td>
                    <div style="font-weight: 600; color: var(--slate-800);">{{ $mut['kategori'] }}</div>
                    <div style="font-size: 0.6875rem; color: var(--slate-500);">{{ $mut['metode'] }}</div>
                  </td>
                  <td>
                    @if($mut['is_lunas'])
                      <span class="badge badge-emerald" style="font-size: 0.6875rem;">✓ Lunas Sah</span>
                    @else
                      <span class="badge badge-amber" style="font-size: 0.6875rem;">⏳ Menunggu Bendahara</span>
                    @endif
                  </td>
                  <td style="text-align: right;">
                    <button type="button" class="btn btn-subtle btn-view-receipt" data-ref="{{ $mut['ref_code'] }}" data-periode="{{ $mut['periode'] }}" data-nominal="{{ $mut['nominal_formatted'] }}" data-tanggal="{{ $mut['tanggal'] }}" style="padding: 4px 8px; font-size: 0.6875rem;">
                      Struk Sah
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align: center; padding: 24px; color: var(--slate-400);">
                    Belum ada catatan mutasi iuran untuk unit rumah ini.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="modal-footer" style="background-color: var(--slate-50); border-top: 1px solid var(--slate-200); padding: 14px 24px; display: flex; justify-content: space-between; align-items: center;">
      <span style="font-size: 0.75rem; color: var(--slate-500);">
        Data kependudukan & mutasi terhubung ke <strong>Database RT 05 / RW 01 Harmoni Residence</strong>
      </span>
      <button type="button" class="btn btn-subtle" data-close-modal>Tutup Jendela</button>
    </div>
  </div>
</div>

<!-- ==========================================================================
     MODAL: EDIT PROFIL WARGA MANDIRI
     ========================================================================== -->
<div class="modal-overlay" id="modalEditProfil" role="dialog" aria-modal="true">
  <div class="modal-container" style="max-width: 520px;">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="modal-title-icon" style="background-color: var(--primary-50); color: var(--primary-600);">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
          </svg>
        </div>
        <div>
          <h3 class="modal-title">Edit Profil Warga</h3>
          <div class="modal-subtitle">Perbarui data kontak dan kata sandi akun portal mandiri Anda</div>
        </div>
      </div>
      <button class="modal-close-btn" data-close-modal aria-label="Tutup Modal">&times;</button>
    </div>

    <form id="formEditProfil" action="{{ route('portal.edit-profil') }}" method="POST">
      @csrf
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Nomor Induk Kependudukan (NIK)</label>
          <input type="text" class="form-input" value="{{ $warga->NIK ?? '' }}" readonly style="background-color: #F1F5F9; color: #64748B; cursor: not-allowed;">
          <div class="form-hint">NIK adalah identitas kependudukan permanen yang terikat pada data RT.</div>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor Kartu Keluarga (KK)</label>
          <input type="text" class="form-input" value="{{ $warga->ID_KK ?? '' }}" readonly style="background-color: #F1F5F9; color: #64748B; cursor: not-allowed;">
        </div>

        <div class="form-group">
          <label class="form-label">Nama Lengkap Sesuai KTP <span class="req">*</span></label>
          <input type="text" class="form-input" id="editNamaLengkap" name="nama_lengkap" value="{{ $warga->Nama_Lengkap ?? '' }}" required>
        </div>

        <div class="form-group">
          <label class="form-label">Nomor Telepon / WhatsApp <span class="req">*</span></label>
          <input type="tel" class="form-input" id="editNoTelp" name="no_telp" value="{{ $warga->no_telp ?? '' }}" placeholder="08xxxxxxxxxx" required>
        </div>

        <div class="form-group">
          <label class="form-label">Alamat Email (Akun Google)</label>
          <input type="email" class="form-input" id="editEmail" name="email" value="{{ $warga->email ?? '' }}" placeholder="contoh@gmail.com">
          <div class="form-hint">Email dapat digunakan untuk masuk cepat melalui Google SSO.</div>
        </div>

        <!-- Google OAuth Account Link Section -->
        <div style="background-color: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 12px; padding: 12px 14px; margin-top: 14px;">
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
              <svg width="20" height="20" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.66v3.05h3.87c2.26-2.09 3.67-5.17 3.67-9.15z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.87-3.05c-1.08.72-2.45 1.16-4.06 1.16-3.13 0-5.78-2.11-6.73-4.96H1.28v3.15C3.3 21.36 7.37 24 12 24z"/>
                <path fill="#FBBC05" d="M5.27 14.24A7.18 7.18 0 0 1 4.9 12c0-.78.14-1.54.37-2.24V6.61H1.28A11.97 11.97 0 0 0 0 12c0 1.92.45 3.74 1.28 5.39l3.99-3.15z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.37 0 3.3 2.64 1.28 6.61l3.99 3.15c.95-2.85 3.6-4.96 6.73-4.96z"/>
              </svg>
              <div>
                <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A;">Akun Google</div>
                <div style="font-size: 0.75rem; color: #64748B;">
                  @if(!empty($warga->google_id))
                    Terhubung: <strong style="color: #059669;">{{ $warga->email ?? 'Aktif' }}</strong>
                  @else
                    Belum ditautkan ke akun Google
                  @endif
                </div>
              </div>
            </div>

            @if(empty($warga->google_id))
              <a href="{{ route('auth.google.redirect') }}" class="btn btn-subtle btn-sm" style="font-size: 0.75rem; padding: 6px 12px; font-weight: 600; text-decoration: none;">
                Tautkan Google
              </a>
            @endif
          </div>
        </div>

        <div style="border-top: 1px solid #E2E8F0; padding-top: 14px; margin-top: 14px;">
          <div style="font-size: 0.8125rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">Ubah Kata Sandi (Opsional)</div>
          <div class="form-group">
            <label class="form-label">Kata Sandi Baru</label>
            <input type="password" class="form-input" id="editPassword" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
          </div>
          <div class="form-group">
            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
            <input type="password" class="form-input" id="editPasswordConfirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" minlength="6">
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-subtle" data-close-modal>Batal</button>
        <button type="submit" class="btn btn-primary" id="btnSubmitEditProfil">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Global Tab Switching function for Payment Modal
window.switchPaymentMethodTab = function(tab) {
  const tabBtnQris = document.getElementById('tabBtnQris');
  const tabBtnBank = document.getElementById('tabBtnBank');
  const tabPaneQris = document.getElementById('tabPaneQris');
  const tabPaneBank = document.getElementById('tabPaneBank');

  if (tab === 'qris') {
    if (tabBtnQris) {
      tabBtnQris.style.background = '#FFFFFF';
      tabBtnQris.style.color = '#047857';
      tabBtnQris.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
      tabBtnQris.style.fontWeight = '800';
    }
    if (tabBtnBank) {
      tabBtnBank.style.background = 'transparent';
      tabBtnBank.style.color = '#64748B';
      tabBtnBank.style.boxShadow = 'none';
      tabBtnBank.style.fontWeight = '700';
    }
    if (tabPaneQris) tabPaneQris.style.display = 'block';
    if (tabPaneBank) tabPaneBank.style.display = 'none';
  } else {
    if (tabBtnBank) {
      tabBtnBank.style.background = '#FFFFFF';
      tabBtnBank.style.color = '#1E40AF';
      tabBtnBank.style.boxShadow = '0 1px 3px rgba(0,0,0,0.1)';
      tabBtnBank.style.fontWeight = '800';
    }
    if (tabBtnQris) {
      tabBtnQris.style.background = 'transparent';
      tabBtnQris.style.color = '#64748B';
      tabBtnQris.style.boxShadow = 'none';
      tabBtnQris.style.fontWeight = '700';
    }
    if (tabPaneBank) tabPaneBank.style.display = 'block';
    if (tabPaneQris) tabPaneQris.style.display = 'none';
  }
};

// Global Copy to Clipboard helper with button feedback
window.copyText = function(text, btnElement, successLabel = 'Tersalin!') {
  function applyFeedback() {
    if (btnElement) {
      const originalHtml = btnElement.innerHTML;
      const originalBg = btnElement.style.background;
      const originalColor = btnElement.style.color;
      btnElement.innerHTML = '<span>✓ ' + successLabel + '</span>';
      btnElement.style.background = '#10B981';
      btnElement.style.color = '#FFFFFF';
      setTimeout(() => {
        btnElement.innerHTML = originalHtml;
        btnElement.style.background = originalBg;
        btnElement.style.color = originalColor;
      }, 2000);
    }
  }

  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).then(applyFeedback).catch(() => {
      fallbackCopy(text);
      applyFeedback();
    });
  } else {
    fallbackCopy(text);
    applyFeedback();
  }

  function fallbackCopy(val) {
    const ta = document.createElement('textarea');
    ta.value = val;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); } catch (e) {}
    document.body.removeChild(ta);
  }
};

document.addEventListener('DOMContentLoaded', function () {
  const btnBayarMidtrans = document.getElementById('btnBayarMidtransSnap');
  const btnBukaTransfer = document.getElementById('btnBukaTransferRekening');
  const btnSnapText = document.getElementById('btnSnapText');
  const btnSimulate = document.getElementById('btnSimulateSuccess');
  const btnExecuteQris = document.getElementById('btnExecuteQrisSimulation');
  const btnExecuteBank = document.getElementById('btnExecuteBankTransferSimulation');

  function getSelectedTagihanPayload() {
    const selectedRadio = document.querySelector('input[name="tagihan_id"]:checked');
    if (!selectedRadio) {
      return { pay_all: true };
    }
    if (selectedRadio.value === 'all') {
      return { pay_all: true };
    }
    return { tagihan_id: parseInt(selectedRadio.value, 10) };
  }

  function updatePaymentModalAmounts(nominal, orderId = null, qrCodeUrl = null) {
    const formatted = 'Rp ' + Math.round(nominal).toLocaleString('id-ID');
    const qrisTotalEl = document.getElementById('qrisModalTotalAmount');
    const bankTotalEl = document.getElementById('bankModalTotalAmount');
    const qrisOrderEl = document.getElementById('qrisModalOrderId');
    const qrisImg = document.getElementById('qrisImageCode');
    const simUrlInput = document.getElementById('qrisCodeImageUrlInput');
    const directNominalEl = document.getElementById('directQrisNominalText');
    const directQrisImg = document.getElementById('directQrisCodeImage');

    if (directNominalEl) directNominalEl.textContent = formatted;
    if (directQrisImg) {
      const qrData = '00020101021226590014ID.GO.GPN.WWW0118936000020110200391050215000000000000000520459995303360540' + Math.round(nominal) + '5802ID5927KAS RT 05 HARMONI RESIDENCE6006BOGOR61051611062200716TAG-IURAN-RT056304';
      directQrisImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data=' + encodeURIComponent(qrData);
      directQrisImg.style.display = 'block';
    }

    if (qrisTotalEl) qrisTotalEl.textContent = formatted;
    if (bankTotalEl) bankTotalEl.textContent = formatted;
    if (orderId && qrisOrderEl) qrisOrderEl.textContent = 'Order ID: ' + orderId;

    if (qrCodeUrl) {
      if (simUrlInput) simUrlInput.value = qrCodeUrl;
      if (qrisImg) qrisImg.src = qrCodeUrl;
    } else {
      const demoUrl = 'https://api.sandbox.midtrans.com/v2/qris/demo-' + (orderId ? orderId.replace(/[^a-zA-Z0-9-]/g, '') : Math.round(nominal)) + '/qr-code';
      if (simUrlInput) simUrlInput.value = demoUrl;
      if (qrisImg) {
        const qrData = '00020101021226590014ID.GO.GPN.WWW0118936000020110200391050215000000000000000520459995303360540' + Math.round(nominal) + '5802ID5927KAS RT 05 HARMONI RESIDENCE6006BOGOR61051611062200716TAG-IURAN-RT056304';
        qrisImg.src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=8&data=' + encodeURIComponent(qrData);
      }
    }
  }

  // Live Midtrans Sandbox QRIS integration
  async function fetchLiveMidtransQris() {
    const payload = getSelectedTagihanPayload();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    try {
      const response = await fetch("{{ route('payment.snap-token') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(payload)
      });
      const data = await response.json();
      if (data && data.success) {
        if (data.qr_code_url) {
          const directQrisImg = document.getElementById('directQrisCodeImage');
          if (directQrisImg) {
            directQrisImg.src = data.qr_code_url;
            directQrisImg.style.display = 'block';
            const fallbackEl = document.getElementById('directQrisSvgFallback');
            if (fallbackEl) fallbackEl.style.display = 'none';
          }
          window.currentMidtransQrUrl = data.qr_code_url;
          window.currentMidtransQrString = data.qr_string;
        }
        if (data.order_id) {
          window.currentOrderId = data.order_id;
          startPaymentStatusPolling(data.order_id);
        }
      }
    } catch (e) {
      console.warn('[Midtrans Live QRIS]', e);
    }
  }

  // Auto-fetch live Midtrans QR when opening modal
  document.querySelectorAll('[data-modal="modalIuran"]').forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      setTimeout(fetchLiveMidtransQris, 250);
    });
  });

  // Update dynamic total amount and button text when selecting different bill radio
  const billRadios = document.querySelectorAll('input[name="tagihan_id"]');
  billRadios.forEach(function (radio) {
    radio.addEventListener('change', function () {
      const nominal = parseFloat(this.dataset.nominal || 0);
      const totalDisplay = document.getElementById('iuranTotalAmount');
      if (totalDisplay) {
        totalDisplay.textContent = 'Rp ' + Math.round(nominal).toLocaleString('id-ID');
      }
      updatePaymentModalAmounts(nominal);
      fetchLiveMidtransQris();
    });
  });

  // Direct QRIS Simulation Button Handler
  const btnSimulasiDirectQris = document.getElementById('btnSimulasiDirectQris');
  if (btnSimulasiDirectQris) {
    btnSimulasiDirectQris.addEventListener('click', async function (e) {
      e.preventDefault();
      const payload = getSelectedTagihanPayload();
      
      const originalHtml = btnSimulasiDirectQris.innerHTML;
      btnSimulasiDirectQris.disabled = true;
      btnSimulasiDirectQris.innerHTML = '<span>⏳ Memproses Verifikasi QRIS...</span>';

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      try {
        const response = await fetch("{{ route('payment.simulate-success') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (data.success) {
          alert('Pembayaran Berhasil! Tagihan iuran kas RT Anda telah otomatis terverifikasi LUNAS seketika via QRIS.');
          window.location.reload();
        } else {
          alert(data.message || 'Gagal memproses simulasi QRIS.');
          btnSimulasiDirectQris.disabled = false;
          btnSimulasiDirectQris.innerHTML = originalHtml;
        }
      } catch (err) {
        alert('Error: ' + err.message);
        btnSimulasiDirectQris.disabled = false;
        btnSimulasiDirectQris.innerHTML = originalHtml;
      }
    });
  }

  // Toggle Direct Bank Account List Handler
  const btnToggleDirectBankList = document.getElementById('btnToggleDirectBankList');
  const directBankListContainer = document.getElementById('directBankListContainer');
  if (btnToggleDirectBankList && directBankListContainer) {
    btnToggleDirectBankList.addEventListener('click', function (e) {
      e.preventDefault();
      const isHidden = directBankListContainer.style.display === 'none' || directBankListContainer.style.display === '';
      if (isHidden) {
        directBankListContainer.style.display = 'block';
        btnToggleDirectBankList.innerHTML = '<span>🏦 Tutup Rekening Bank ▴</span>';
      } else {
        directBankListContainer.style.display = 'none';
        btnToggleDirectBankList.innerHTML = '<span>🏦 Opsi Rekening Bank Kas RT ▾</span>';
      }
    });
  }

  // Countdown Timer
  let countdownSecs = 15 * 60 - 1;
  const countdownEl = document.getElementById('paymentTimerCountdown');
  if (countdownEl) {
    setInterval(function() {
      if (countdownSecs <= 0) return;
      countdownSecs--;
      const mins = Math.floor(countdownSecs / 60);
      const secs = countdownSecs % 60;
      countdownEl.textContent = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
    }, 1000);
  }

  // 1. Eksekusi Pembayaran via Midtrans Snap / Buka QRIS Popup
  if (btnBayarMidtrans) {
    btnBayarMidtrans.addEventListener('click', async function (e) {
      e.preventDefault();
      const payload = getSelectedTagihanPayload();

      const originalHtml = btnSnapText ? btnSnapText.innerHTML : 'Scan QRIS';
      btnBayarMidtrans.disabled = true;
      if (btnSnapText) btnSnapText.innerHTML = 'Memuat QRIS...';

      try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch("{{ route('payment.snap-token') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok || !data.success || !data.snap_token) {
          throw new Error(data.message || 'Gagal membuat tiket pembayaran Midtrans.');
        }

        // Buka Popup QRIS Interaktif Langsung (menampilkan QR asli Midtrans & link simulator)
        const modalIuranEl = document.getElementById('modalIuran');
        const modalQrisEl = document.getElementById('modalQrisPopup');
        
        updatePaymentModalAmounts(data.total_amount, data.order_id, data.qr_code_url);
        switchPaymentMethodTab('qris');
        window.currentOrderId = data.order_id;
        startPaymentStatusPolling(data.order_id);

        if (modalIuranEl) modalIuranEl.classList.remove('active');
        if (modalQrisEl) {
          modalQrisEl.classList.add('active');
          document.body.style.overflow = 'hidden';
        }

      } catch (err) {
        console.error('[Payment Gateway Error]', err);
        alert('Pemberitahuan: ' + err.message);
      } finally {
        btnBayarMidtrans.disabled = false;
        if (btnSnapText) btnSnapText.innerHTML = originalHtml;
      }
    });
  }

  // -------------------------------------------------------------
  // Real-time Payment Status Polling (Auto-Detect QRIS & Simulator)
  // -------------------------------------------------------------
  let paymentPollTimer = null;

  function stopPaymentPolling() {
    if (paymentPollTimer) {
      clearInterval(paymentPollTimer);
      paymentPollTimer = null;
    }
  }

  function startPaymentStatusPolling(orderId) {
    stopPaymentPolling();
    if (!orderId) return;

    paymentPollTimer = setInterval(async function () {
      try {
        const response = await fetch("{{ route('payment.check-status') }}?order_id=" + encodeURIComponent(orderId));
        if (!response.ok) return;
        const checkData = await response.json();

        if (checkData.success && checkData.is_paid) {
          stopPaymentPolling();

          // Celebration display inside popup
          const tabPaneQris = document.getElementById('tabPaneQris');
          if (tabPaneQris) {
            tabPaneQris.innerHTML = `
              <div style="padding: 30px 16px; text-align: center;">
                <div style="width: 76px; height: 76px; margin: 0 auto 16px auto; background: linear-gradient(135deg, #DCFCE7 0%, #BBF7D0 100%); border: 3px solid #22C55E; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.25rem; box-shadow: 0 10px 25px rgba(34, 197, 94, 0.3);">
                  ✓
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 900; color: #065F46; margin: 0 0 6px 0;">
                  Pembayaran Berhasil Diterima!
                </h3>
                <p style="font-size: 0.875rem; color: #047857; margin: 0 0 14px 0; line-height: 1.4;">
                  Tagihan kas RT Anda telah otomatis diverifikasi <strong>LUNAS</strong> seketika setelah QR di-scan.
                </p>
                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: #F1F5F9; border-radius: 9999px; font-size: 0.75rem; color: #64748B;">
                  <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                  <span>Memperbarui status portal warga...</span>
                </div>
              </div>
            `;
          }

          setTimeout(function () {
            window.location.reload();
          }, 1800);
        }
      } catch (e) {
        // Silently keep polling
      }
    }, 2500);
  }

  // Clear polling when closing modals
  document.querySelectorAll('[data-close-modal]').forEach(function (btn) {
    btn.addEventListener('click', stopPaymentPolling);
  });

  // 2. Tombol Buka Transfer No. Rekening Bank Langsung
  if (btnBukaTransfer) {
    btnBukaTransfer.addEventListener('click', function(e) {
      e.preventDefault();
      const modalIuranEl = document.getElementById('modalIuran');
      const modalQrisEl = document.getElementById('modalQrisPopup');
      
      const selectedRadio = document.querySelector('input[name="tagihan_id"]:checked');
      const nominal = selectedRadio ? parseFloat(selectedRadio.dataset.nominal || 0) : 75000;
      updatePaymentModalAmounts(nominal);
      
      switchPaymentMethodTab('bank');

      if (modalIuranEl) modalIuranEl.classList.remove('active');
      if (modalQrisEl) {
        modalQrisEl.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });
  }

  // 3. Eksekusi Pembayaran Sukses di dalam Modal QRIS
  if (btnExecuteQris) {
    btnExecuteQris.addEventListener('click', async function (e) {
      e.preventDefault();
      btnExecuteQris.disabled = true;
      btnExecuteQris.innerHTML = 'Memproses Verifikasi QRIS...';

      const payload = getSelectedTagihanPayload();
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      try {
        const response = await fetch("{{ route('payment.simulate-success') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (data.success) {
          alert('Pembayaran Berhasil! Tagihan iuran kas RT Anda telah otomatis tercatat LUNAS via QRIS.');
          window.location.reload();
        } else {
          alert(data.message || 'Gagal memproses verifikasi QRIS.');
          btnExecuteQris.disabled = false;
        }
      } catch (err) {
        alert('Error: ' + err.message);
        btnExecuteQris.disabled = false;
      }
    });
  }

  // 4. Eksekusi Konfirmasi Transfer Bank Selesai
  if (btnExecuteBank) {
    btnExecuteBank.addEventListener('click', async function (e) {
      e.preventDefault();
      btnExecuteBank.disabled = true;
      btnExecuteBank.innerHTML = 'Memverifikasi Transfer Bank...';

      const payload = getSelectedTagihanPayload();
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      try {
        const response = await fetch("{{ route('payment.simulate-success') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (data.success) {
          alert('Pembayaran Berhasil! Transfer ke rekening kas RT telah terverifikasi dan tagihan Anda otomatis tercatat LUNAS.');
          window.location.reload();
        } else {
          alert(data.message || 'Gagal memproses verifikasi transfer.');
          btnExecuteBank.disabled = false;
        }
      } catch (err) {
        alert('Error: ' + err.message);
        btnExecuteBank.disabled = false;
      }
    });
  }

  // 5. Simulasi Sandbox Demo Sukses Langsung untuk Testing
  if (btnSimulate) {
    btnSimulate.addEventListener('click', async function (e) {
      e.preventDefault();
      const payload = getSelectedTagihanPayload();

      if (!confirm('Simulasikan pembayaran kas sukses untuk tagihan yang dipilih? (Fitur demo testing)')) {
        return;
      }

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      try {
        const response = await fetch("{{ route('payment.simulate-success') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify(payload)
        });

        const data = await response.json();
        if (data.success) {
          alert('Sukses! Pembayaran otomatis terverifikasi LUNAS.');
          window.location.reload();
        } else {
          alert(data.message || 'Gagal simulasi');
        }
      } catch (err) {
        alert('Error: ' + err.message);
      }
    });
  }
});
</script>
