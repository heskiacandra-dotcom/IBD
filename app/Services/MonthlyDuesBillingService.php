<?php

namespace App\Services;

use App\Models\Pemasukan;
use App\Models\KartuKeluarga;
use App\Models\Warga;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MonthlyDuesBillingService
{
    /**
     * Standard monthly dues amount per household (Rp 75.000).
     */
    public const DEFAULT_MONTHLY_DUES = 75000.00;

    /**
     * Ensure a monthly dues bill exists for a household for a given month.
     * Citizens are free to pay on ANY date within the month (due date = end of month).
     */
    public function ensureMonthlyBillForHousehold(string|int $idKk, int|string|null $idRt = null, ?Carbon $date = null): Pemasukan
    {
        $date = $date ? $date->copy() : Carbon::now();
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // If ID_RT is not passed, resolve from KartuKeluarga or Warga
        if (!$idRt) {
            $idRt = KartuKeluarga::where('ID_KK', $idKk)->value('ID_RT')
                ?? Warga::where('ID_KK', $idKk)->value('ID_RT')
                ?? 1;
        }

        // Check if bill already exists for this KK in this month (by tgl_jatuh_tmp within this month or matching month name)
        $monthName = $date->locale('id')->translatedFormat('F Y');
        $namaTagihan = "Iuran Keamanan & Kebersihan {$monthName}";

        $existing = Pemasukan::withoutGlobalScope('tenant')
            ->where('ID_KK', $idKk)
            ->where(function ($q) use ($startOfMonth, $endOfMonth, $namaTagihan) {
                $q->whereBetween('tgl_jatuh_tmp', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                  ->orWhere('nama_tagihan', 'like', "%{$namaTagihan}%");
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        // Create new monthly bill for current month with due date at end of month
        $newBill = Pemasukan::withoutGlobalScope('tenant')->create([
            'ID_KK' => (string) $idKk,
            'ID_RT' => $idRt,
            'Kategori' => 'Iuran Warga',
            'nama_tagihan' => $namaTagihan,
            'nominal' => self::DEFAULT_MONTHLY_DUES,
            'tgl_jatuh_tmp' => $endOfMonth->toDateString(), // Bebas bayar di tanggal berapa saja sebelum akhir bulan
            'status_tagihan' => 'Belum Lunas',
        ]);

        Log::info("[MONTHLY-DUES] Generated monthly bill for KK {$idKk} for period {$monthName}: Tagihan #{$newBill->Tagihan_Id}");

        return $newBill;
    }

    /**
     * Compute comprehensive billing summary for a household, including:
     * - Automatic rollover detection
     * - Multi-month accumulation / double billing if previous months are unpaid
     * - Full breakdown of overdue months vs current month
     */
    public function getHouseholdBillingSummary(string|int $idKk, int|string|null $idRt = null, ?Carbon $date = null): array
    {
        $date = $date ? $date->copy() : Carbon::now();
        $this->ensureMonthlyBillForHousehold($idKk, $idRt, $date);

        // Fetch all unpaid bills for this household
        $unpaidBills = Pemasukan::withoutGlobalScope('tenant')
            ->where('ID_KK', $idKk)
            ->whereRaw('LOWER(status_tagihan) != ?', ['lunas'])
            ->orderBy('tgl_jatuh_tmp', 'asc')
            ->get();

        $unpaidCount = $unpaidBills->count();
        $totalNominal = (float) $unpaidBills->sum('nominal');
        $isDouble = ($unpaidCount >= 2);

        $currentMonthStart = $date->copy()->startOfMonth();
        $currentMonthEnd = $date->copy()->endOfMonth();

        $breakdown = $unpaidBills->map(function ($bill) use ($currentMonthStart) {
            $dueDate = Carbon::parse($bill->tgl_jatuh_tmp);
            $isOverdueMonth = $dueDate->lt($currentMonthStart);

            return [
                'tagihan_id' => $bill->Tagihan_Id,
                'nama_tagihan' => $bill->nama_tagihan,
                'nominal' => (float) $bill->nominal,
                'nominal_formatted' => 'Rp ' . number_format($bill->nominal, 0, ',', '.'),
                'tgl_jatuh_tmp' => $dueDate->locale('id')->translatedFormat('d F Y'),
                'is_overdue_month' => $isOverdueMonth,
                'badge_text' => $isOverdueMonth ? 'Menunggak (Bulan Lalu)' : 'Bulan Berjalan',
                'badge_class' => $isOverdueMonth ? 'badge-rose' : 'badge-amber',
                'status_tagihan' => $bill->status_tagihan,
            ];
        });

        // Notice and status explanation
        if ($unpaidCount === 0) {
            $statusLabel = 'Lunas';
            $statusBadge = 'badge-emerald';
            $noticeText = 'Semua iuran kas keluarga Anda lunas. Terima kasih atas partisipasi Anda dalam membangun lingkungan RT!';
        } elseif ($isDouble) {
            $statusLabel = "Tagihan Menunggak ({$unpaidCount} Bulan - Berlipat/Double)";
            $statusBadge = 'badge-rose';
            $noticeText = "Perhatian: Anda memiliki {$unpaidCount} bulan tagihan yang belum diselesaikan. Karena sudah berganti bulan, tagihan terakumulasi menjadi Rp " . number_format($totalNominal, 0, ',', '.') . " ({$unpaidCount}x lipat). Anda bebas membayar di tanggal berapa saja sebelum " . $currentMonthEnd->locale('id')->translatedFormat('d F Y') . ".";
        } else {
            $statusLabel = 'Tagihan Bulan Berjalan (1 Bulan)';
            $statusBadge = 'badge-amber';
            $noticeText = "Tagihan iuran kas bulan ini aktif. Anda dapat membayar di tanggal berapa saja sebelum berganti bulan (" . $currentMonthEnd->locale('id')->translatedFormat('d F Y') . ").";
        }

        return [
            'id_kk' => (string) $idKk,
            'unpaid_count' => $unpaidCount,
            'is_double' => $isDouble,
            'multiplier' => max($unpaidCount, 1),
            'total_nominal' => $totalNominal,
            'total_nominal_formatted' => 'Rp ' . number_format($totalNominal, 0, ',', '.'),
            'status_label' => $statusLabel,
            'status_badge' => $statusBadge,
            'due_date_text' => "Bebas bayar kapan saja hingga " . $currentMonthEnd->locale('id')->translatedFormat('d F Y'),
            'notice_text' => $noticeText,
            'bills' => $breakdown->values()->all(),
            'raw_bills' => $unpaidBills,
        ];
    }

    /**
     * Generate monthly dues for all registered households across all RTs.
     * Can be invoked via artisan command or cron scheduler at the start of each month.
     */
    public function generateMonthlyBillsForAllHouseholds(?Carbon $date = null): int
    {
        $date = $date ? $date->copy() : Carbon::now();
        $households = KartuKeluarga::select('ID_KK', 'ID_RT')->get();

        $createdCount = 0;
        foreach ($households as $kk) {
            $monthName = $date->locale('id')->translatedFormat('F Y');
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $exists = Pemasukan::withoutGlobalScope('tenant')
                ->where('ID_KK', $kk->ID_KK)
                ->where(function ($q) use ($startOfMonth, $endOfMonth, $monthName) {
                    $q->whereBetween('tgl_jatuh_tmp', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                      ->orWhere('nama_tagihan', 'like', "%{$monthName}%");
                })
                ->exists();

            if (!$exists) {
                $this->ensureMonthlyBillForHousehold($kk->ID_KK, $kk->ID_RT, $date);
                $createdCount++;
            }
        }

        return $createdCount;
    }
}
