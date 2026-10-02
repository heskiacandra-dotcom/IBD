<div class="space-y-5 p-1 text-slate-800">
    {{-- Ringkasan Data Tagihan Card --}}
    <div class="bg-slate-50/80 border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs">
        <div class="flex items-center justify-between border-b border-slate-200/70 pb-3 mb-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-bold">
                    TAG
                </span>
                <div>
                    <h4 class="text-xs uppercase tracking-wider font-semibold text-slate-400">Ringkasan Tagihan</h4>
                    <p class="text-sm font-bold text-slate-900">{{ $record->nama_tagihan }}</p>
                </div>
            </div>
            <div>
                @php
                    $status = strtolower($record->status_tagihan ?? '');
                @endphp
                @if($status === 'lunas')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                        Lunas Terverifikasi
                    </span>
                @elseif(in_array($status, ['pending_approval', 'menunggu verifikasi', 'menunggu', 'pending']))
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                        Menunggu Verifikasi
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-slate-400"></span>
                        {{ ucfirst($record->status_tagihan ?? 'Belum Lunas') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                <span class="text-slate-400 block font-medium mb-0.5">Kepala Keluarga / KK</span>
                <span class="font-bold text-slate-800 text-sm block truncate" title="{{ $record->nama_kepala_keluarga }}">
                    {{ $record->nama_kepala_keluarga }}
                </span>
                <span class="text-[11px] text-slate-500 font-mono">No. {{ $record->ID_KK }}</span>
            </div>

            <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                <span class="text-slate-400 block font-medium mb-0.5">Kategori Iuran</span>
                <span class="font-semibold text-slate-800 text-sm block">
                    {{ $record->Kategori ?? 'Iuran Warga' }}
                </span>
                <span class="text-[11px] text-slate-500">Kas Lingkungan RT</span>
            </div>

            <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                <span class="text-slate-400 block font-medium mb-0.5">Nominal Tagihan</span>
                <span class="font-black text-emerald-600 text-base block tracking-tight">
                    Rp {{ number_format((float) $record->nominal, 0, ',', '.') }}
                </span>
                <span class="text-[11px] text-slate-400">ID: #TAG-{{ str_pad($record->Tagihan_Id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div class="bg-white p-3 rounded-xl border border-slate-200/60 shadow-2xs">
                <span class="text-slate-400 block font-medium mb-0.5">Jatuh Tempo / Upload</span>
                <span class="font-semibold text-slate-800 text-sm block">
                    {{ $record->tgl_jatuh_tmp ? \Carbon\Carbon::parse($record->tgl_jatuh_tmp)->translatedFormat('d M Y') : '-' }}
                </span>
                <span class="text-[11px] text-slate-500">
                    {{ $record->updated_at ? $record->updated_at->diffForHumans() : 'Baru saja' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Area Preview Bukti Transfer --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-xs flex flex-col items-center justify-center">
        @php
            $hasProof = $record->hasValidBuktiBayar();
            $proofUrl = $record->bukti_bayar_url;
        @endphp

        @if($hasProof && $proofUrl)
            <div class="w-full flex flex-col items-center">
                {{-- Header Preview with Open Fullscreen Button --}}
                <div class="w-full flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-xs font-semibold text-slate-700">Berkas Bukti Transfer Terlampir</span>
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
                         alt="Bukti Transfer Warga"
                         class="max-h-[480px] w-auto max-w-full object-contain rounded-lg shadow-sm hover:scale-[1.01] transition-transform duration-200"
                         loading="lazy" />
                </div>
                
                <p class="text-[11px] text-slate-400 mt-3 text-center">
                    Klik tombol "Buka Gambar Ukuran Penuh" di atas untuk memeriksa nomor referensi bank, cap stempel, atau nominal transfer secara detail.
                </p>
            </div>
        @else
            {{-- Fallback Handling: Belum ada bukti transfer yang diunggah warga --}}
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
                    Belum ada bukti transfer yang diunggah warga
                </h5>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Warga pemegang KK No. {{ $record->ID_KK }} belum mengunggah struk atau bukti transfer perbankan untuk tagihan iuran ini melalui Portal Mandiri Warga.
                </p>
            </div>
        @endif
    </div>
</div>
