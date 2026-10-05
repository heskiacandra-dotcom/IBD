<div class="space-y-6">
    @php
        $activeCount = $loans->where('status', 'borrowed')->count();
        $pendingCount = $loans->where('status', 'pending')->count();
        $returnedCount = $loans->where('status', 'returned')->count();
    @endphp

    <!-- Stats Row -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 dark:border-amber-900/40 dark:bg-amber-950/20">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-800 dark:text-amber-300">Sedang Dipinjam</span>
            <div class="mt-1 text-2xl font-black text-amber-900 dark:text-amber-100">{{ $activeCount }} Transaksi</div>
            <p class="mt-0.5 text-[11px] text-amber-700/80 dark:text-amber-400">Aset berada di tangan warga</p>
        </div>

        <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4 dark:border-blue-900/40 dark:bg-blue-950/20">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-800 dark:text-blue-300">Menunggu Verifikasi</span>
            <div class="mt-1 text-2xl font-black text-blue-900 dark:text-blue-100">{{ $pendingCount }} Pengajuan</div>
            <p class="mt-0.5 text-[11px] text-blue-700/80 dark:text-blue-400">Permohonan pinjam via portal</p>
        </div>

        <div class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300">Selesai Dikembalikan</span>
            <div class="mt-1 text-2xl font-black text-emerald-900 dark:text-emerald-100">{{ $returnedCount }} Riwayat</div>
            <p class="mt-0.5 text-[11px] text-emerald-700/80 dark:text-emerald-400">Barang kembali dalam kondisi baik</p>
        </div>
    </div>

    <!-- Table List -->
    <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="w-full text-left text-xs">
            <thead class="border-b border-gray-200 bg-gray-50 text-[11px] font-bold uppercase text-gray-500 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Nama Alat / Aset</th>
                    <th class="px-4 py-3">Warga Peminjam</th>
                    <th class="px-4 py-3">Periode Peminjaman</th>
                    <th class="px-4 py-3">Tujuan / Acara</th>
                    <th class="px-4 py-3 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($loans as $loan)
                    @php
                        $st = strtolower($loan->status ?? 'pending');
                        $badgeBg = match($st) {
                            'borrowed' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-900',
                            'pending' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-900',
                            'returned' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 dark:border-emerald-900',
                            default => 'bg-gray-50 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700',
                        };
                        $statusLabel = match($st) {
                            'borrowed' => 'Sedang Dipinjam',
                            'pending' => 'Menunggu Konfirmasi',
                            'returned' => 'Sudah Dikembalikan',
                            'rejected' => 'Ditolak',
                            default => ucfirst($st),
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                            <div>{{ $loan->nama_alat }}</div>
                            <span class="font-mono text-[10px] text-gray-500">{{ $loan->kode_aset ?? $loan->kode_pinjam }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-gray-900 dark:text-white">{{ $loan->nama_peminjam }}</div>
                            <div class="mt-0.5 flex items-center gap-1.5 text-[11px] text-gray-500 dark:text-gray-400">
                                @if($loan->kontak)
                                    @php
                                        $waClean = preg_replace('/[^0-9]/', '', $loan->kontak);
                                        if (str_starts_with($waClean, '0')) {
                                            $waClean = '62' . substr($waClean, 1);
                                        }
                                    @endphp
                                    <a href="https://wa.me/{{ $waClean }}" target="_blank" class="font-medium text-emerald-600 hover:underline">
                                        📱 {{ $loan->kontak }}
                                    </a>
                                @endif
                                <span>• 🏠 {{ $loan->blok_rumah ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px] text-gray-700 dark:text-gray-300">
                            <div>Mulai: {{ $loan->tgl_mulai ? \Illuminate\Support\Carbon::parse($loan->tgl_mulai)->translatedFormat('d M Y') : '-' }}</div>
                            <div>Sampai: {{ $loan->tgl_selesai ? \Illuminate\Support\Carbon::parse($loan->tgl_selesai)->translatedFormat('d M Y') : '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $loan->tujuan }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="inline-block rounded border px-2 py-0.5 text-[11px] font-semibold {{ $badgeBg }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500 dark:text-gray-400">
                            Belum ada catatan peminjaman aset.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
