<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Filament\Support\RawJs;
use Illuminate\Support\Carbon;

class ArusKasChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Arus Kas';

    protected ?string $description = 'Pemasukan vs Pengeluaran';

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'md' => 4,
        'xl' => 6,
    ];

    protected ?string $maxHeight = '180px';

    protected function getData(): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $currentYear = Carbon::now()->year;

        $user = auth()->user();
        $isSuperAdmin = $user && ($user->role === 'super_admin');
        $userRtId = $user?->ID_RT;

        // 1. Query Pemasukan (Cash In - Iuran Lunas)
        $pemasukanQuery = Pemasukan::withoutGlobalScope('tenant')
            ->whereRaw("LOWER(status_tagihan) = 'lunas'")
            ->whereYear('tgl_jatuh_tmp', $currentYear);

        if (!$isSuperAdmin && $userRtId) {
            $pemasukanQuery->where('ID_RT', $userRtId);
        }

        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $monthPemSql = $driver === 'sqlite' ? "CAST(strftime('%m', tgl_jatuh_tmp) AS INTEGER)" : "MONTH(tgl_jatuh_tmp)";
        $monthPengSql = $driver === 'sqlite' ? "CAST(strftime('%m', Tgl_Bayar) AS INTEGER)" : "MONTH(Tgl_Bayar)";

        $pemasukanMonthly = (clone $pemasukanQuery)
            ->selectRaw("{$monthPemSql} as bulan, SUM(nominal) as total")
            ->groupByRaw($monthPemSql)
            ->pluck('total', 'bulan')
            ->toArray();

        // 2. Query Pengeluaran (Cash Out - Operasional)
        $pengeluaranQuery = Pengeluaran::query()
            ->whereYear('Tgl_Bayar', $currentYear);

        if (!$isSuperAdmin && $userRtId) {
            $pengeluaranQuery->where('ID_RT', $userRtId);
        }

        $pengeluaranMonthly = (clone $pengeluaranQuery)
            ->selectRaw("{$monthPengSql} as bulan, SUM(Jumlah_Bayar) as total")
            ->groupByRaw($monthPengSql)
            ->pluck('total', 'bulan')
            ->toArray();

        $inflowData = [];
        $outflowData = [];

        for ($m = 1; $m <= 12; $m++) {
            $inflowData[] = (float) ($pemasukanMonthly[$m] ?? 0);
            $outflowData[] = (float) ($pengeluaranMonthly[$m] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Kas Masuk (Iuran Lunas)',
                    'data' => $inflowData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.85)',
                    'borderColor' => '#10B981',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                    'barPercentage' => 0.6,
                    'categoryPercentage' => 0.7,
                ],
                [
                    'label' => 'Kas Keluar (Operasional)',
                    'data' => $outflowData,
                    'backgroundColor' => 'rgba(244, 63, 94, 0.85)',
                    'borderColor' => '#F43F5E',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                    'borderSkipped' => false,
                    'barPercentage' => 0.6,
                    'categoryPercentage' => 0.7,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array|RawJs|null
    {
        return RawJs::make(<<<JS
        {
            layout: {
                padding: {
                    top: 0,
                    right: 14,
                    bottom: 0,
                    left: 2
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 6,
                        padding: 12,
                        font: {
                            family: 'Plus Jakarta Sans, sans-serif',
                            size: 11
                        }
                    }
                },
                tooltip: {
                    padding: 8,
                    cornerRadius: 6,
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || '';
                            var value = Number(context.parsed.y || 0);
                            return ' ' + label + ': Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Plus Jakarta Sans, sans-serif',
                            size: 10
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(241, 245, 249, 0.8)',
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 4,
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + ' Jt';
                            }
                            if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toLocaleString('id-ID') + ' Rb';
                            }
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
                        },
                        font: {
                            family: 'Plus Jakarta Sans, sans-serif',
                            size: 10
                        }
                    }
                }
            }
        }
        JS);
    }
}
