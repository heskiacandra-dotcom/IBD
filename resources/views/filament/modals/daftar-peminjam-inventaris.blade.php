<div class="space-y-6">
    <!-- Header Ringkasan Aset -->
    <div class="rounded-xl border border-gray-200 bg-gray-50/75 p-4 dark:border-gray-700 dark:bg-gray-800/50">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-primary-100 text-primary-600 dark:bg-primary-900/40 dark:text-primary-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="rounded bg-primary-50 px-2 py-0.5 font-mono text-xs font-bold text-primary-700 dark:bg-primary-950 dark:text-primary-300">
                            {{ $inventaris->Kode_Barang }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">📍 {{ $inventaris->Lokasi ?? 'Balai RT' }}</span>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $inventaris->Nama_Barang }}</h3>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold {{ (int)$inventaris->Jumlah > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300' : 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/50 dark:text-rose-300' }}">
                    <span class="h-2 w-2 rounded-full {{ (int)$inventaris->Jumlah > 0 ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    Stok Tersedia: {{ $inventaris->Jumlah }} Unit
                </span>
            </div>
        </div>
    </div>

    @php
        $activeBorrowers = $peminjamList->where('is_active', true);
        $pastBorrowers = $peminjamList->where('is_active', false);
    @endphp

    <!-- Section 1: Peminjam Aktif (Sedang Meminjam / Menunggu) -->
    <div>
        <div class="mb-3 flex items-center justify-between">
            <h4 class="flex items-center gap-2 text-sm font-bold text-gray-900 dark:text-white">
                <span class="flex h-2 w-2 rounded-full bg-amber-500"></span>
                Peminjam Aktif Saat Ini ({{ $activeBorrowers->count() }})
            </h4>
            <span class="text-xs text-gray-500 dark:text-gray-400">Harap pantau tanggal pengembalian barang</span>
        </div>

        @if($activeBorrowers->isNotEmpty())
            <div class="space-y-3">
                @foreach($activeBorrowers as $p)
                    <div class="relative overflow-hidden rounded-xl border border-amber-200/80 bg-amber-50/30 p-4 shadow-sm transition hover:shadow dark:border-amber-900/50 dark:bg-amber-950/20">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-800 dark:bg-amber-900/60 dark:text-amber-200">
                                    {{ strtoupper(substr($p['nama'], 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $p['nama'] }}</span>
                                        <span class="rounded px-2 py-0.5 text-xs font-semibold {{ $p['status_color'] === 'warning' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                            {{ $p['status_label'] }}
                                        </span>
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-600 dark:text-gray-400">
                                        @if($p['kontak'] && $p['kontak'] !== '-')
                                            @php
                                                $waClean = preg_replace('/[^0-9]/', '', $p['kontak']);
                                                if (str_starts_with($waClean, '0')) {
                                                    $waClean = '62' . substr($waClean, 1);
                                                }
                                            @endphp
                                            <a href="https://wa.me/{{ $waClean }}" target="_blank" class="inline-flex items-center gap-1 font-semibold text-emerald-600 hover:underline dark:text-emerald-400">
                                                <span>📱 {{ $p['kontak'] }}</span>
                                                <span class="text-[10px]">(Chat WA)</span>
                                            </a>
                                        @else
                                            <span>📱 {{ $p['kontak'] }}</span>
                                        @endif
                                        <span>🏠 {{ $p['alamat'] }}</span>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-700 dark:text-gray-300">
                                        <span class="font-semibold text-gray-900 dark:text-white">Keperluan:</span> {{ $p['tujuan'] }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">Periode Pinjam</div>
                                <div class="font-mono text-xs font-bold text-gray-900 dark:text-white">
                                    {{ $p['tgl_mulai'] }} s.d {{ $p['tgl_selesai'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center dark:border-gray-700">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Saat ini tidak ada warga yang sedang meminjam barang ini.</p>
            </div>
        @endif
    </div>

    <!-- Section 2: Riwayat Peminjaman Selesai (Arsip) -->
    <div>
        <div class="mb-3">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                Riwayat Pengembalian & Arsip Peminjaman ({{ $pastBorrowers->count() }})
            </h4>
        </div>

        @if($pastBorrowers->isNotEmpty())
            <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold uppercase text-gray-500 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Nama Warga</th>
                            <th class="px-4 py-3">Kontak / Alamat</th>
                            <th class="px-4 py-3">Periode Pinjam</th>
                            <th class="px-4 py-3">Keperluan</th>
                            <th class="px-4 py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pastBorrowers as $hist)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                    {{ $hist['nama'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $hist['kontak'] }} • {{ $hist['alamat'] }}
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-600 dark:text-gray-400">
                                    {{ $hist['tgl_mulai'] }} - {{ $hist['tgl_selesai'] }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                    {{ $hist['tujuan'] }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="rounded px-2 py-0.5 text-[11px] font-semibold {{ $hist['status_color'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950 dark:text-rose-300' }}">
                                        {{ $hist['status_label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="rounded-lg bg-gray-50 p-4 text-center text-xs text-gray-500 dark:bg-gray-800/40 dark:text-gray-400">
                Belum ada catatan riwayat peminjaman masa lalu.
            </div>
        @endif
    </div>
</div>
