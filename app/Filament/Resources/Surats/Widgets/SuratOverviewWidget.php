<?php

namespace App\Filament\Resources\Surats\Widgets;

use App\Models\Surat;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SuratOverviewWidget extends BaseStatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $query = Surat::query();
        if ($user && $user->role !== 'super_admin' && $user->ID_RT) {
            $query->whereHas('warga', fn ($q) => $q->where('ID_RT', $user->ID_RT));
        }

        $total = (clone $query)->count();
        $pending = (clone $query)->whereIn(DB::raw('LOWER(status)'), ['pending', 'menunggu'])->count();
        $approved = (clone $query)->whereIn(DB::raw('LOWER(status)'), ['approved', 'disetujui', 'selesai'])->count();
        $rejected = (clone $query)->whereIn(DB::raw('LOWER(status)'), ['rejected', 'ditolak'])->count();

        return [
            Stat::make('Total Pengajuan', $total . ' Berkas')
                ->description('Seluruh arsip permohonan surat')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('primary'),

            Stat::make('Menunggu Tindakan', $pending . ' Surat')
                ->description($pending > 0 ? 'Perlu approval pengurus' : 'Tidak ada antrean tertunda')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pending > 0 ? 'warning' : 'gray'),

            Stat::make('Disetujui / Terbit', $approved . ' Surat')
                ->description('Resmi ber-Nomor Registrasi')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Ditolak', $rejected . ' Surat')
                ->description($rejected > 0 ? 'Tidak memenuhi persyaratan' : 'Nihil penolakan')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($rejected > 0 ? 'danger' : 'gray'),
        ];
    }
}
