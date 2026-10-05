<footer class="site-footer" id="kontak">
  <div class="container">

    <!-- ====================================================================
         EMERGENCY HOTLINES BOX
         ==================================================================== -->
    <div class="emergency-banner">
      <div class="emergency-header">
        <div class="emergency-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
          </svg>
        </div>
        <div class="emergency-title-box">
          <div style="display: inline-flex; align-items: center; gap: 6px; padding: 2px 8px; background: #FEE2E2; border-radius: 6px; font-size: 0.6875rem; font-weight: 800; color: #DC2626; margin-bottom: 4px; text-transform: uppercase;">
            <span>🚨 Siaga 24 Jam</span>
          </div>
          <h3>Pusat Kontak Cepat & Darurat Lingkungan</h3>
          <p>Simpan atau hubungi nomor berikut untuk situasi gawat darurat medis, keamanan, atau pemadam kebakaran di area RT 05.</p>
        </div>
      </div>

@php
    $kontakDaruratList = \App\Models\KontakRt::where('is_aktif', true)
        ->where('lokasi_tampilan', 'footer_darurat')
        ->orderBy('urutan')
        ->get();

    if ($kontakDaruratList->isEmpty()) {
        $kontakDaruratList = collect([
            new \App\Models\KontakRt([
                'role' => 'Ketua RT 05',
                'nama_kontak' => 'Bpk. Sukardi Hartono',
                'nomor_telepon' => '0812-3456-7890',
                'is_whatsapp' => true,
                'pesan_wa' => 'Halo Ketua RT 05, saya warga Harmoni Residence',
            ]),
            new \App\Models\KontakRt([
                'role' => 'Pos Ronda & Keamanan',
                'nama_kontak' => 'Piket Regu Sekuriti',
                'nomor_telepon' => '0821-9876-5432',
                'is_whatsapp' => true,
                'pesan_wa' => 'Halo Pos Ronda RT 05, saya warga ingin melapor keadaan keamanan',
            ]),
            new \App\Models\KontakRt([
                'role' => 'Bhabinkamtibmas Polsek',
                'nama_kontak' => 'Aiptu Hendro Wibowo',
                'nomor_telepon' => '0813-8822-1100',
                'is_whatsapp' => true,
                'pesan_wa' => 'Halo Bhabinkamtibmas Polsek, saya warga RT 05 Harmoni Residence',
            ]),
            new \App\Models\KontakRt([
                'role' => 'Ambulans & Damkar',
                'nama_kontak' => 'Posko Siaga Darurat',
                'nomor_telepon' => '112',
                'is_whatsapp' => false,
            ]),
        ]);
    }

    $kontakSekretariat = \App\Models\KontakRt::where('is_aktif', true)
        ->where('lokasi_tampilan', 'sekretariat')
        ->first() ?? new \App\Models\KontakRt([
            'role' => 'Sekretariat Lingkungan',
            'nama_kontak' => 'Balai Warga RT 05 / RW 01',
            'nomor_telepon' => '0812-3456-7890',
            'is_whatsapp' => true,
            'pesan_wa' => 'Halo Pengurus RT 05, saya warga Harmoni Residence',
        ]);
@endphp

      <div class="emergency-grid">
        @foreach($kontakDaruratList as $kontak)
          <div class="emergency-card">
            <div>
              <div class="emergency-role">{{ $kontak->role }}</div>
              <div class="emergency-name">{{ $kontak->nama_kontak }}</div>
            </div>
            <div style="display: flex; gap: 6px; align-items: center; width: 100%;">
              @if($kontak->is_whatsapp && $kontak->whatsapp_url)
                <a href="{{ $kontak->whatsapp_url }}" target="_blank" rel="noopener" class="emergency-btn-call emergency-btn-wa" title="Hubungi langsung via WhatsApp" style="flex: 1; text-decoration: none;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: #059669;">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                  </svg>
                  <span>{{ $kontak->nomor_telepon }}</span>
                </a>
              @else
                <a href="{{ $kontak->tel_url }}" class="emergency-btn-call" title="Panggil Nomor Telepon Darurat" style="flex: 1; text-decoration: none;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                  </svg>
                  <span>{{ $kontak->nomor_telepon == '112' ? 'Hubungi 112 (Bebas Pulsa)' : $kontak->nomor_telepon }}</span>
                </a>
              @endif
              <button type="button" class="emergency-copy-btn btn-copy-phone" data-phone="{{ $kontak->nomor_telepon }}" title="Salin nomor telepon" style="padding: 6px 8px; border-radius: 8px; border: 1px solid #FECDD3; background: #FFFFFF; color: #BE123C; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; transition: all 0.2s;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="14" height="14" x="8" y="8" rx="2" ry="2"/>
                  <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/>
                </svg>
              </button>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <hr class="footer-divider">

    <!-- ====================================================================
         MAIN FOOTER 4 COLUMNS
         ==================================================================== -->
    <div class="footer-main-grid">

      <!-- Column 1: Brand & Profile -->
      <div class="footer-about">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            </svg>
          </div>
          <div>
            <div style="font-size: 1.1875rem; font-weight: 800; color: #0F172A; line-height: 1;">
              Ruang<span style="color: #2563EB;">Warga</span>
            </div>
            <div style="font-size: 0.6875rem; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 3px;">
              RT 05 / RW 01 Harmoni Residence
            </div>
          </div>
        </div>

        <p>
          Platform digital terpadu pelayanan mandiri dan transparansi keuangan lingkungan. Mewujudkan warga yang rukun, transparan, guyub, dan serba mudah melalui teknologi modern berbasis Laravel.
        </p>

        <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; background: #EFF6FF; border: 1px solid #DBEAFE; border-radius: 8px; font-size: 0.75rem; color: #1E40AF; font-weight: 600;">
          <span style="width: 8px; height: 8px; border-radius: 50%; background: #2563EB; display: inline-block;"></span>
          <span>Layanan Mandiri Warga RT 05</span>
        </div>
      </div>

      <!-- Column 2: Layanan Warga -->
      <div>
        <h4 class="footer-col-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2563EB;">
            <rect width="18" height="18" x="3" y="3" rx="2"/>
            <path d="m9 12 2 2 4-4"/>
          </svg>
          <span>Layanan Warga</span>
        </h4>
        <ul class="footer-links">
          <li>
            <a href="{{ route('portal.layanan') }}">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Pusat Layanan Mandiri</span>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)" data-modal="modalSurat">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Pengajuan Surat Pengantar</span>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)" data-modal="modalIuran">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Bayar Iuran Kas Bulanan</span>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)" data-modal="modalPinjam">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Peminjaman Tenda & Alat</span>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)" data-modal="modalEditProfil">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Ubah Profil & Kontak</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Column 3: Informasi & Transparansi -->
      <div>
        <h4 class="footer-col-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2563EB;">
            <line x1="12" x2="12" y1="2" y2="22"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
          <span>Informasi Publik</span>
        </h4>
        <ul class="footer-links">
          <li>
            <a href="{{ route('portal.agenda') }}">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Jadwal Agenda & Ronda RT</span>
            </a>
          </li>
          <li>
            <a href="{{ route('portal.transparansi') }}">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Rekap Transparansi Kas</span>
            </a>
          </li>
          <li>
            <a href="{{ route('portal.aspirasi') }}">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Kotak Aspirasi Warga</span>
            </a>
          </li>
          <li>
            <a href="{{ route('portal.agenda') }}#formUsulKegiatanSection">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Usul Kegiatan Lingkungan</span>
            </a>
          </li>
          <li>
            <a href="javascript:void(0)" data-modal="modalStatusWarga">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
              <span>Status Surat & Mutasi Iuran</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Column 4: Sekretariat & Jam Piket -->
      <div>
        <h4 class="footer-col-title">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2563EB;">
            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
          <span>Sekretariat Lingkungan</span>
        </h4>
        <div class="sekretariat-box">
          <div style="font-weight: 800; color: #0F172A; margin-bottom: 4px; font-size: 0.875rem;">
            Balai Warga RT 05 / RW 01
          </div>
          <div style="color: #64748B; margin-bottom: 12px; line-height: 1.4;">
            Jl. Harmoni Residence No. 01, Kel. Sukamaju, Kec. Cilodong, Kota Depok 16415.
          </div>

          <div style="border-top: 1px dashed #CBD5E1; padding-top: 10px; margin-bottom: 12px;">
            <div style="font-weight: 700; color: #334155; display: flex; align-items: center; gap: 6px; margin-bottom: 4px;">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
              <span>Jam Piket Pengurus RT:</span>
            </div>
            <div style="font-size: 0.75rem; color: #64748B;">
              <div>• Senin - Jumat: 19.00 - 21.00 WIB</div>
              <div>• Sabtu - Minggu: 09.00 - 12.00 WIB</div>
            </div>
          </div>

          <a href="{{ $kontakSekretariat->whatsapp_url ?? 'https://wa.me/6281234567890' }}" target="_blank" rel="noopener" style="display: flex; align-items: center; justify-content: center; gap: 6px; width: 100%; background: #059669; color: #FFFFFF; font-weight: 700; font-size: 0.75rem; padding: 8px 12px; border-radius: 8px; text-decoration: none; transition: background 0.2s;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            <span>Hubungi Sekretariat RT ({{ $kontakSekretariat->nomor_telepon ?? '0812-3456-7890' }})</span>
          </a>
        </div>
      </div>

    </div>

    <!-- ====================================================================
         FOOTER BOTTOM COPYRIGHT & STATUS
         ==================================================================== -->
    <div class="footer-bottom">
      <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <div>&copy; 2026 <strong>RuangWarga</strong> — Sistem Informasi Manajemen Lingkungan Guyub Rukun.</div>
        <div style="display: inline-flex; align-items: center; gap: 5px; padding: 2px 8px; background: #F1F5F9; border-radius: 9999px; font-size: 0.6875rem; color: #475569; font-weight: 600;">
          <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
          <span>Sistem Online</span>
        </div>
      </div>
      <div style="display: flex; gap: 16px; align-items: center;">
        <a href="javascript:void(0)" onclick="alert('Kebijakan Privasi: Data kependudukan terenkripsi dan diisolasi khusus wilayah RT 05.')">Kebijakan Privasi</a>
        <span>•</span>
        <a href="javascript:void(0)" onclick="alert('Pedoman Warga: Menjunjung tinggi asas musyawarah mufakat, gotong royong, dan kerukunan.')">Pedoman Kerukunan Warga</a>
        <span>•</span>
        <a href="{{ route('portal.aspirasi') }}">Bantuan & Masukan</a>
      </div>
    </div>

  </div>
</footer>
