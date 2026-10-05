<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MonthlyDuesBillingService;
use Illuminate\Support\Carbon;

class GenerateMonthlyDuesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iuran:generate-monthly {--month= : Target month in YYYY-MM format (default: current month)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly dues bills for all households. Unpaid bills will automatically accumulate / double when month changes.';

    /**
     * Execute the console command.
     */
    public function handle(MonthlyDuesBillingService $billingService): int
    {
        $monthOption = $this->option('month');
        $date = $monthOption ? Carbon::parse($monthOption . '-01') : Carbon::now();

        $this->info("Memproses penerbitan iuran kas bulanan untuk periode: " . $date->locale('id')->translatedFormat('F Y'));

        $count = $billingService->generateMonthlyBillsForAllHouseholds($date);

        $this->info("Berhasil! {$count} tagihan baru telah diterbitkan untuk keluarga yang belum memiliki tagihan bulan ini.");
        $this->comment("Catatan: Warga bebas membayar di tanggal berapa saja sebelum akhir bulan (" . $date->copy()->endOfMonth()->translatedFormat('d F Y') . "). Tagihan yang belum dibayar akan otomatis menumpuk/double saat berganti bulan.");

        return Command::SUCCESS;
    }
}
