<div class="space-y-5 p-1 text-slate-800">
    @php
        $tableSumber = strtoupper($record->tabel_sumber ?? '');
        $status = strtolower($record->status ?? '');
        $hasProof = $record->hasValidBukti();
        $proofUrl = $record->bukti_url;

        $pemasukan = null;
        $pengeluaran = null;
        $surat = null;
        $inventaris = null;
        $kegiatan = null;

        if ($tableSumber === 'PEMASUKAN') {
            $pemasukan = \App\Models\Pemasukan::withoutGlobalScope('tenant')->with(['kartuKeluarga.warga'])->find($record->id_referensi);
        } elseif ($tableSumber === 'PENGELUARAN') {
            $pengeluaran = \App\Models\Pengeluaran::find($record->id_referensi);
        } elseif ($tableSumber === 'SURAT') {
            $surat = \App\Models\Surat::with('warga')->find($record->id_referensi);
        } elseif ($tableSumber === 'INVENTARIS') {
            $inventaris = \App\Models\Inventaris::find($record->id_referensi);
        } elseif ($tableSumber === 'KEGIATAN') {
            $kegiatan = \App\Models\Kegiatan::find($record->id_referensi);
        }
    @endphp

    {{-- Header Banner Tiket --}}
    <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs">
        <div class="flex items-center justify-between border-b border-slate-200/70 pb-3 mb-4">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-blue-50 text-blue-700 border border-blue-200 text-xs font-black">
                    #APP
                </span>
                <div>
                    <h4 class="text-xs uppercase tracking-wider font-semibold text-slate-400">Verifikasi Pengajuan Pengurus</h4>
                    <p class="text-sm font-bold text-slate-900">
                        Tiket #APP-{{ str_pad($record->id_approval, 4, '0', STR_PAD_LEFT) }} • {{ $tableSumber }}
                    </p>
                </div>
            </div>

            <div>
                @if($status === 'disetujui' || $status === 'approved')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                        Disetujui
                    </span>
                @elseif(in_array($status, ['menunggu', 'pending']))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Menunggu Tindakan
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 border border-rose-200">
                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-rose-500"></span>
                        {{ ucfirst($record->status) }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Metadata Grid berdasarkan Kategori --}}
        @if($tableSumber === 'PEMASUKAN' && $pemasukan)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Pemohon / Kepala Keluarga</span>
                    <span class="font-bold text-slate-800 text-sm block truncate" title="{{ $pemasukan->nama_kepala_keluarga }}">
                        {{ $pemasukan->nama_kepala_keluarga }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-mono">No. KK: {{ $pemasukan->ID_KK }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Jenis Tagihan</span>
                    <span class="font-bold text-slate-800 text-sm block truncate">
                        {{ $pemasukan->nama_tagihan }}
                    </span>
                    <span class="text-[11px] text-slate-500">{{ $pemasukan->Kategori }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Nominal Setoran</span>
                    <span class="font-black text-emerald-600 text-base block tracking-tight">
                        Rp {{ number_format((float) $pemasukan->nominal, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-slate-500">Ref: #TAG-{{ str_pad($pemasukan->Tagihan_Id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        @elseif($tableSumber === 'PENGELUARAN' && $pengeluaran)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Uraian Belanja</span>
                    <span class="font-bold text-slate-800 text-sm block truncate" title="{{ $pengeluaran->Keterangan }}">
                        {{ $pengeluaran->Keterangan }}
                    </span>
                    <span class="text-[11px] text-slate-500">Kategori: {{ $pengeluaran->Kategori }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Jumlah Bayar</span>
                    <span class="font-black text-rose-600 text-base block tracking-tight">
                        Rp {{ number_format((float) $pengeluaran->Jumlah_Bayar, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] text-slate-500">Metode: {{ $pengeluaran->Metode_Bayar ?? 'Transfer / Tunai' }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Tanggal Belanja</span>
                    <span class="font-semibold text-slate-800 text-sm block">
                        {{ $pengeluaran->Tgl_Bayar ? \Carbon\Carbon::parse($pengeluaran->Tgl_Bayar)->translatedFormat('d M Y') : '-' }}
                    </span>
                    <span class="text-[11px] text-slate-500">Ref: #OUT-{{ str_pad($pengeluaran->Pengeluaran_id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        @elseif($tableSumber === 'SURAT' && $surat)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Warga Pemohon</span>
                    <span class="font-bold text-slate-800 text-sm block">
                        {{ $surat->warga->Nama_Lengkap ?? 'Warga' }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-mono">NIK: {{ $surat->NIK }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Jenis Surat</span>
                    <span class="font-bold text-slate-800 text-sm block">
                        {{ $surat->Jenis_surat }}
                    </span>
                    <span class="text-[11px] text-slate-500">Pelayanan Administrasi</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Tanggal Pengajuan</span>
                    <span class="font-semibold text-slate-800 text-sm block">
                        {{ $surat->created_at ? $surat->created_at->translatedFormat('d M Y, H:i') : '-' }}
                    </span>
                    <span class="text-[11px] text-slate-500">ID: #SRT-{{ $surat->surat_id }}</span>
                </div>
            </div>
        @elseif($tableSumber === 'INVENTARIS' && $inventaris)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Nama Fasilitas / Aset</span>
                    <span class="font-bold text-slate-800 text-sm block">
                        {{ $inventaris->Nama_Barang }}
                    </span>
                    <span class="text-[11px] text-slate-500 font-mono">Kode: {{ $inventaris->Kode_Barang }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Ketersediaan Stok</span>
                    <span class="font-bold text-slate-800 text-sm block">
                        {{ $inventaris->Jumlah }} Unit Tersedia
                    </span>
                    <span class="text-[11px] text-slate-500">Lokasi: {{ $inventaris->Lokasi ?? 'Balai RT' }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Uraian Peminjaman</span>
                    <p class="font-medium text-slate-700 text-xs">
                        {{ $record->catatan ?: $record->source_detail }}
                    </p>
                </div>
            </div>
        @elseif($tableSumber === 'KEGIATAN' && $kegiatan)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Agenda Kegiatan</span>
                    <span class="font-bold text-slate-800 text-sm block">
                        {{ $kegiatan->nama_kegiatan }}
                    </span>
                    <span class="text-[11px] text-slate-500">Status: {{ $kegiatan->status }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Jadwal & Lokasi</span>
                    <span class="font-bold text-slate-800 text-sm block">
                        {{ $kegiatan->tgl_kegiatan ? \Carbon\Carbon::parse($kegiatan->tgl_kegiatan)->translatedFormat('d M Y') : '-' }}
                    </span>
                    <span class="text-[11px] text-slate-500">Lokasi: {{ $kegiatan->lokasi ?? 'Balai Warga RT' }}</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                    <span class="text-slate-400 block font-medium mb-0.5">Deskripsi Usulan</span>
                    <p class="font-medium text-slate-700 text-xs">
                        {{ $kegiatan->deskripsi ?: $record->source_detail }}
                    </p>
                </div>
            </div>
        @else
            <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs text-xs">
                <span class="text-slate-400 block font-medium mb-0.5">Rincian Referensi</span>
                <p class="font-semibold text-slate-800 text-sm">{{ $record->source_detail }}</p>
                @if($record->catatan)
                    <p class="text-slate-500 mt-1 italic">{{ $record->catatan }}</p>
                @endif
            </div>
        @endif
    </div>

    {{-- Area Preview Berkas Bukti (Bukti Transfer / Nota Pengeluaran) --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-xs flex flex-col items-center justify-center">
        @if($hasProof && $proofUrl)
            <div class="w-full flex flex-col items-center">
                {{-- Toolbar Preview --}}
                <div class="w-full flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        @if($tableSumber === 'PEMASUKAN')
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-slate-800">Berkas Bukti Transfer Pemohon</span>
                        @elseif($tableSumber === 'PENGELUARAN')
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span class="text-xs font-bold text-slate-800">Berkas Nota / Kuitansi Belanja Operasional</span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="text-xs font-bold text-slate-800">Lampiran Berkas Pendukung</span>
                        @endif
                    </div>
                    <a href="{{ $proofUrl }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition">
                        <span>Buka Gambar Ukuran Penuh</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>

                {{-- Responsive Image Frame --}}
                <div class="w-full bg-slate-50 border border-slate-200/80 rounded-xl p-2 sm:p-4 flex items-center justify-center overflow-hidden">
                    <img src="{{ $proofUrl }}"
                         alt="Pratinjau Berkas Tiket Approval"
                         class="max-h-[480px] w-auto max-w-full object-contain rounded-lg shadow-sm hover:scale-[1.01] transition-transform duration-200"
                         loading="lazy" />
                </div>

                <p class="text-[11px] text-slate-400 mt-3 text-center">
                    Gunakan tombol di atas atau kontrol persetujuan di bawah untuk menyelesaikan verifikasi tiket ini.
                </p>
            </div>
        @else
            {{-- Fallback Handling: Belum ada bukti transfer / nota yang diunggah --}}
            <div class="py-12 px-6 flex flex-col items-center justify-center text-center max-w-md mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 mb-4 shadow-2xs">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>

                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200 mb-2">
                    Belum Ada Bukti
                </span>

                <h5 class="text-sm font-bold text-slate-700 mb-1">
                    @if($tableSumber === 'PEMASUKAN')
                        Belum ada bukti transfer yang diunggah warga
                    @elseif($tableSumber === 'PENGELUARAN')
                        Belum ada nota atau kuitansi yang dilampirkan
                    @else
                        Belum ada berkas lampiran untuk tiket ini
                    @endif
                </h5>
                <p class="text-xs text-slate-500 leading-relaxed">
                    @if($tableSumber === 'PEMASUKAN')
                        Warga belum melampirkan berkas bukti pembayaran atau struk transfer untuk diverifikasi oleh bendahara.
                    @elseif($tableSumber === 'PENGELUARAN')
                        Nota fisik atau faktur pembelian operasional belum diunggah ke dalam sistem.
                    @else
                        Pengajuan ini tidak menyertakan berkas digital pendukung.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
