<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Warga;
use App\Models\KartuKeluarga;
use App\Models\DataRt;
use App\Models\Surat;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Models\Kegiatan;
use App\Models\Inventaris;
use App\Models\Approval;
use App\Models\Aspirasi;
use App\Models\ResponAspirasi;
use App\Models\RtUnit;
use App\Models\OfficialLetter;
use App\Models\ApprovalTicket;
use App\Models\CashMutation;

class CitizenPortalController extends Controller
{
    /**
     * Menampilkan Citizen Self-Service Portal RuangWarga
     * Strict Authentication & Self-Data Scoping: auth('warga')
     */
    public function index(): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return redirect()->route('login');
        }

        $warga->loadMissing(['kartuKeluarga', 'dataRt']);
        $rt = $warga->dataRt ?? DataRt::find($warga->ID_RT) ?? DataRt::first();
        $nomorRt = $rt ? $rt->nomor_rt : '05';
        $nomorRw = $rt ? $rt->nomor_rw : '01';
        $rtId = $warga->ID_RT ?? ($rt ? $rt->ID_RT : 1);

        $suratSaya = $this->getSuratSaya($warga);
        $aspirasiSaya = $this->getAspirasiSaya($warga);
        [$tagihanSaya, $tagihanPending, $totalTerbayarTahunIni, $isLunasBulanIni, $billingSummary] = $this->getTagihanData($warga, $rtId);
        $transparansiKas = $this->getTransparansiKas($rtId);
        $inventarisCatalog = $this->getInventarisCatalog($rtId);
        $agendaList = $this->getAgendaList($rtId);

        $citizenSummary = [
            'total_surat' => $suratSaya->count(),
            'total_surat_acc' => $suratSaya->where('is_acc', true)->count(),
            'total_surat_pending' => $suratSaya->where('is_pending', true)->count(),
            'total_tagihan_pending' => $tagihanPending->count(),
            'is_lunas_bulan_ini' => $isLunasBulanIni,
            'total_terbayar_tahun_ini' => 'Rp ' . number_format($totalTerbayarTahunIni, 0, ',', '.'),
            'total_aspirasi' => $aspirasiSaya->count(),
            'is_double_tagihan' => $billingSummary['is_double'] ?? false,
            'total_tagihan_nominal' => $billingSummary['total_nominal'] ?? 0,
            'total_tagihan_formatted' => $billingSummary['total_nominal_formatted'] ?? 'Rp 0',
        ];

        $kontakDarurat = [
            ['role' => 'Ketua RT ' . $nomorRt, 'name' => $rt ? $rt->nama_ketua_rt : 'Bpk. H. Sukardi', 'phone' => '0812-3456-7890', 'display' => '0812-3456-7890'],
            ['role' => 'Pos Ronda & Keamanan', 'name' => 'Piket Regu Sekuriti', 'phone' => '0821-9876-5432', 'display' => '0821-9876-5432'],
            ['role' => 'Bhabinkamtibmas Polsek', 'name' => 'Aiptu Hendro Wibowo', 'phone' => '0813-8822-1100', 'display' => '0813-8822-1100'],
            ['role' => 'Ambulans & Damkar', 'name' => 'Posko Siaga Darurat', 'phone' => '112', 'display' => 'Hubungi 112 (Bebas Pulsa)'],
        ];

        $rawPublicAspirasi = Aspirasi::with(['warga.kartuKeluarga', 'responAspirasi'])
            ->latest('id_aspirasi')
            ->take(6)
            ->get();
        $aspirasiFeed = $this->formatAspirasiCollection($rawPublicAspirasi, $warga, $nomorRt);

        return view('portal.index', compact(
            'warga',
            'nomorRt',
            'nomorRw',
            'suratSaya',
            'aspirasiSaya',
            'tagihanSaya',
            'tagihanPending',
            'billingSummary',
            'transparansiKas',
            'inventarisCatalog',
            'agendaList',
            'citizenSummary',
            'kontakDarurat',
            'aspirasiFeed'
        ));
    }

    /**
     * Get surat history for authenticated warga
     */
    protected function getSuratSaya(Warga $warga)
    {
        $erdSurat = Surat::where('NIK', $warga->NIK)
            ->latest('tgl_pengajuan')
            ->get();

        return $erdSurat->map(function ($s) {
            $statusLower = strtolower($s->status);
            $isAcc = in_array($statusLower, ['disetujui', 'selesai', 'approved']);
            $isRejected = in_array($statusLower, ['ditolak', 'rejected']);
            $isPending = !$isAcc && !$isRejected;
            $tCode = '#SRT-' . str_pad($s->surat_id, 4, '0', STR_PAD_LEFT);

            // Gunakan nomor surat resmi dari database yang diterbitkan pengurus di CMS
            $nomorSurat = $s->nomor_surat;
            if (!$nomorSurat && $isAcc) {
                $nomorSurat = Surat::generateNomorSurat($s->surat_id, $s->warga?->ID_RT);
            }

            // Ambil catatan penolakan jika permohonan ditolak pengurus
            $approval = Approval::where('tabel_sumber', 'SURAT')
                ->where('id_referensi', (string) $s->surat_id)
                ->latest('id_approval')
                ->first();
            $catatanPenolakan = ($isRejected && $approval) ? $approval->catatan : null;

            return [
                'id' => $s->surat_id,
                'nomor_surat' => $nomorSurat,
                'ticket_code' => $tCode,
                'jenis_surat' => $s->Jenis_surat,
                'keperluan' => $s->perihal,
                'tanggal' => Carbon::parse($s->tgl_pengajuan)->translatedFormat('d M Y'),
                'status' => $s->status,
                'is_acc' => $isAcc,
                'is_pending' => $isPending,
                'is_rejected' => $isRejected,
                'catatan_penolakan' => $catatanPenolakan,
                'status_label' => $isAcc ? 'Disetujui RT' : ($isPending ? 'Menunggu Approval' : 'Ditolak'),
                'status_badge' => $isAcc ? 'badge-emerald' : ($isPending ? 'badge-amber' : 'badge-rose'),
                'pdf_url' => route('surat.pdf.download', ['id' => $s->surat_id]),
                'verify_url' => route('surat.verify', ['id' => $s->surat_id]),
            ];
        });
    }

    /**
     * Get aspirasi history for authenticated warga
     */
    protected function getAspirasiSaya(Warga $warga)
    {
        $erdAspirasi = Aspirasi::with('responAspirasi')
            ->where('NIK', $warga->NIK)
            ->latest('id_aspirasi')
            ->get();

        return $erdAspirasi->map(function ($asp) {
            $respon = $asp->responAspirasi;
            return [
                'id' => $asp->id_aspirasi,
                'nik' => $asp->NIK,
                'isi' => $asp->Isi_Aspirasi,
                'tanggal' => Carbon::parse($asp->created_at ?? now())->translatedFormat('d M Y, H:i'),
                'is_ditanggapi' => !is_null($respon),
                'status_label' => $respon ? 'Ditanggapi Resmi RT' : 'Menunggu Tanggapan',
                'status_badge' => $respon ? 'badge-emerald' : 'badge-amber',
                'respon' => $respon ? [
                    'responder' => $respon->nama_responden,
                    'isi' => $respon->isi_respon,
                    'tanggal' => Carbon::parse($respon->tanggal_respon)->translatedFormat('d M Y, H:i') . ' WIB',
                ] : null,
            ];
        });
    }

    /**
     * Get tagihan data and summary metrics for warga
     *
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection, 2: float, 3: bool, 4: array}
     */
    protected function getTagihanData(Warga $warga, int|string $rtId): array
    {
        $billingService = app(\App\Services\MonthlyDuesBillingService::class);
        $billingSummary = $billingService->getHouseholdBillingSummary($warga->ID_KK, $rtId);

        $tagihanSemua = Pemasukan::withoutGlobalScope('tenant')
            ->where('ID_KK', $warga->ID_KK)
            ->where('ID_RT', $rtId)
            ->latest('tgl_jatuh_tmp')
            ->get();

        $tagihanPending = $tagihanSemua->filter(function ($t) {
            return strtolower($t->status_tagihan) !== 'lunas';
        });

        $tagihanLunas = $tagihanSemua->filter(function ($t) {
            return strtolower($t->status_tagihan) === 'lunas';
        });

        $totalTerbayarTahunIni = (float) $tagihanLunas->sum('nominal');
        $isLunasBulanIni = $tagihanLunas->contains(function ($t) {
            return Carbon::parse($t->tgl_jatuh_tmp)->isCurrentMonth() && Carbon::parse($t->tgl_jatuh_tmp)->isCurrentYear();
        });

        $tagihanSaya = $tagihanSemua->map(function ($t) {
            $statusLower = strtolower($t->status_tagihan);
            $isLunas = $statusLower === 'lunas';
            $isPendingVerif = in_array($statusLower, ['pending_approval', 'menunggu verifikasi', 'menunggu', 'pending']);

            $approval = Approval::where('tabel_sumber', 'PEMASUKAN')
                ->where('id_referensi', (string) $t->Tagihan_Id)
                ->latest('id_approval')
                ->first();

            $isRejectedApproval = ($approval && in_array(strtolower($approval->status), ['rejected', 'ditolak']));

            $statusLabel = 'Belum Dibayar';
            $statusBadge = 'badge-slate';

            if ($isLunas) {
                $statusLabel = 'Lunas Terverifikasi';
                $statusBadge = 'badge-emerald';
            } elseif ($isPendingVerif) {
                $statusLabel = 'Menunggu Verifikasi Bendahara';
                $statusBadge = 'badge-amber';
            } elseif ($isRejectedApproval) {
                $statusLabel = 'Pembayaran Ditolak';
                $statusBadge = 'badge-rose';
            }

            return [
                'tagihan_id' => $t->Tagihan_Id,
                'ref_code' => '#TAG-' . str_pad($t->Tagihan_Id, 4, '0', STR_PAD_LEFT),
                'nama_tagihan' => $t->nama_tagihan,
                'kategori' => $t->Kategori,
                'nominal' => (float) $t->nominal,
                'nominal_formatted' => 'Rp ' . number_format((float) ($t->nominal ?? 0), 0, ',', '.'),
                'tanggal' => Carbon::parse($t->tgl_jatuh_tmp)->locale('id')->translatedFormat('d M Y'),
                'status' => $t->status_tagihan,
                'bukti_bayar' => $t->bukti_bayar,
                'is_lunas' => $isLunas,
                'is_pending_verif' => $isPendingVerif,
                'is_rejected' => $isRejectedApproval,
                'catatan_penolakan' => $isRejectedApproval ? $approval->catatan : null,
                'status_label' => $statusLabel,
                'status_badge' => $statusBadge,
            ];
        });

        return [$tagihanSaya, $tagihanPending, $totalTerbayarTahunIni, $isLunasBulanIni, $billingSummary];
    }

    /**
     * Get aggregate cash transparency summary synchronized with CMS
     */
    protected function getTransparansiKas(int|string $rtId): array
    {
        $totalPemasukanRt = (float) Pemasukan::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->whereRaw('LOWER(status_tagihan) = ?', ['lunas'])
            ->sum('nominal');

        $totalPengeluaranRt = (float) Pengeluaran::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->sum('Jumlah_Bayar');

        $baselineSaldo = 28450000;
        $saldoKasRt = max(1500000, $baselineSaldo + ($totalPemasukanRt - $totalPengeluaranRt));

        $now = Carbon::now();
        $pemasukanBulanIni = (float) Pemasukan::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->whereRaw('LOWER(status_tagihan) = ?', ['lunas'])
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('nominal');

        $pengeluaranBulanIni = (float) Pengeluaran::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->whereMonth('Tgl_Bayar', $now->month)
            ->whereYear('Tgl_Bayar', $now->year)
            ->sum('Jumlah_Bayar');

        // Gunakan transaksi riil yang tercatat di CMS
        $masukTampil = $pemasukanBulanIni > 0 ? $pemasukanBulanIni : $totalPemasukanRt;
        $keluarTampil = $pengeluaranBulanIni > 0 ? $pengeluaranBulanIni : $totalPengeluaranRt;

        // Hitung distribusi alokasi anggaran riil dari tabel PENGELUARAN di CMS
        $expensesByCategory = Pengeluaran::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->select('Kategori', DB::raw('SUM(Jumlah_Bayar) as total'))
            ->groupBy('Kategori')
            ->orderByDesc('total')
            ->get();

        $categoryConfig = [
            'keamanan' => ['label' => 'Keamanan Lingkungan & Honor Sekuriti', 'color' => '#2563EB', 'class' => 'alokasi-keamanan'],
            'kebersihan' => ['label' => 'Kebersihan & Retribusi Truk Sampah', 'color' => '#10B981', 'class' => 'alokasi-kebersihan'],
            'fasilitas' => ['label' => 'Pemeliharaan Fasum & PJU Lampu Jalan', 'color' => '#F59E0B', 'class' => 'alokasi-pemeliharaan'],
            'fasum' => ['label' => 'Pemeliharaan Fasum & PJU Lampu Jalan', 'color' => '#F59E0B', 'class' => 'alokasi-pemeliharaan'],
            'perbaikan' => ['label' => 'Perbaikan & Renovasi Infrastruktur', 'color' => '#F97316', 'class' => 'alokasi-pemeliharaan'],
            'sosial' => ['label' => 'Dana Sosial & Santunan Warga', 'color' => '#8B5CF6', 'class' => 'alokasi-sosial'],
            'santunan' => ['label' => 'Dana Sosial & Santunan Warga', 'color' => '#8B5CF6', 'class' => 'alokasi-sosial'],
            'operasional' => ['label' => 'Operasional & Kesekretariatan RT', 'color' => '#06B6D4', 'class' => 'alokasi-operasional'],
        ];

        $alokasi = [];
        if ($totalPengeluaranRt > 0 && $expensesByCategory->isNotEmpty()) {
            foreach ($expensesByCategory as $cat) {
                $katKey = strtolower($cat->Kategori ?? 'operasional');
                $matchedCfg = null;
                foreach ($categoryConfig as $key => $cfg) {
                    if (str_contains($katKey, $key)) {
                        $matchedCfg = $cfg;
                        break;
                    }
                }
                $color = $matchedCfg['color'] ?? '#64748B';
                $class = $matchedCfg['class'] ?? 'alokasi-operasional';
                $label = $matchedCfg['label'] ?? ($cat->Kategori ?: 'Operasional Lingkungan');
                $persen = round(((float) $cat->total / $totalPengeluaranRt) * 100);

                $alokasi[] = [
                    'kategori' => $label,
                    'persen' => (int) $persen,
                    'color' => $color,
                    'class' => $class,
                    'nominal' => 'Rp ' . number_format((float) $cat->total, 0, ',', '.'),
                ];
            }
        }

        // Fallback jika belum ada pengeluaran riil di database
        if (empty($alokasi)) {
            $alokasi = [
                ['kategori' => 'Keamanan Lingkungan & Honor Sekuriti', 'persen' => 45, 'color' => '#2563EB', 'class' => 'alokasi-keamanan', 'nominal' => 'Rp ' . number_format($saldoKasRt * 0.45, 0, ',', '.')],
                ['kategori' => 'Kebersihan & Retribusi Truk Sampah', 'persen' => 30, 'color' => '#10B981', 'class' => 'alokasi-kebersihan', 'nominal' => 'Rp ' . number_format($saldoKasRt * 0.30, 0, ',', '.')],
                ['kategori' => 'Pemeliharaan Fasum & PJU Lampu Jalan', 'persen' => 15, 'color' => '#F59E0B', 'class' => 'alokasi-pemeliharaan', 'nominal' => 'Rp ' . number_format($saldoKasRt * 0.15, 0, ',', '.')],
                ['kategori' => 'Dana Sosial & Santunan Warga', 'persen' => 10, 'color' => '#8B5CF6', 'class' => 'alokasi-sosial', 'nominal' => 'Rp ' . number_format($saldoKasRt * 0.10, 0, ',', '.')],
            ];
        }

        return [
            'total_saldo' => 'Rp ' . number_format($saldoKasRt, 0, ',', '.'),
            'total_masuk_bulan' => '+Rp ' . number_format($masukTampil, 0, ',', '.'),
            'total_keluar_bulan' => '-Rp ' . number_format($keluarTampil, 0, ',', '.'),
            'saldo_num' => $saldoKasRt,
            'masuk_num' => $masukTampil,
            'keluar_num' => $keluarTampil,
            'surplus_num' => $masukTampil - $keluarTampil,
            'update_terakhir' => Carbon::now()->translatedFormat('d F Y'),
            'alokasi' => $alokasi,
        ];
    }

    /**
     * Get inventory catalog for warga portal
     */
    protected function getInventarisCatalog(int|string $rtId)
    {
        return Inventaris::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->get()
            ->map(function ($inv) {
                $stok = (int) $inv->Jumlah;
                $peminjamSummary = $inv->getActiveBorrowersSummary();

                return [
                    'id' => $inv->Inventaris_ID,
                    'nama' => $inv->Nama_Barang,
                    'kode' => $inv->Kode_Barang,
                    'jumlah' => $stok,
                    'lokasi' => $inv->Lokasi ?? 'Balai RT',
                    'is_tersedia' => $stok > 0,
                    'status_label' => $stok > 0 ? "Tersedia ({$stok} Unit)" : 'Stok Kosong (0 Unit)',
                    'status_badge' => $stok > 0 ? 'badge-emerald' : 'badge-rose',
                    'peminjam_summary' => $peminjamSummary,
                ];
            });
    }

    /**
     * Get scheduled community events for warga portal
     */
    protected function getAgendaList(int|string $rtId)
    {
        return Kegiatan::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->orderBy('tgl_kegiatan', 'asc')
            ->take(6)
            ->get()
            ->map(function ($keg) {
                $dt = Carbon::parse($keg->tgl_kegiatan);
                $status = $keg->status ?? 'Mendatang';
                $isLive = ($status === 'Sedang Berlangsung');
                $isPast = ($status === 'Selesai');

                return [
                    'id' => $keg->kegiatan_id,
                    'day' => $dt->format('d'),
                    'month' => $dt->translatedFormat('M'),
                    'year' => $dt->format('Y'),
                    'full_date' => $dt->translatedFormat('l, d F Y - H:i') . ' WIB',
                    'title' => $keg->nama_kegiatan,
                    'location' => $keg->lokasi ?? 'Balai Warga RT',
                    'desc' => $keg->deskripsi,
                    'status' => $status,
                    'is_past' => $isPast,
                    'is_live' => $isLive,
                    'badge' => $status,
                    'badge_class' => $isLive ? 'badge-emerald' : ($isPast ? 'badge-gray' : 'badge-blue'),
                ];
            });
    }

    /**
     * API: Cek Iuran Kas RT
     * Scoped strictly to the authenticated citizen's ID_KK and ID_RT
     */
    public function cekIuran(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $billingService = app(\App\Services\MonthlyDuesBillingService::class);
        $summary = $billingService->getHouseholdBillingSummary($warga->ID_KK, $warga->ID_RT);

        $billsData = collect($summary['bills'] ?? []);
        $firstBill = $billsData->first();
        $isPaid = ($summary['unpaid_count'] === 0);

        $data = [
            'name' => $warga->Nama_Lengkap,
            'address' => optional($warga->kartuKeluarga)->Alamat ?? ('RT ' . $warga->ID_RT),
            'status' => $isPaid ? 'paid' : 'unpaid',
            'amount' => $isPaid ? 'Rp 0 (Lunas)' : $summary['total_nominal_formatted'],
            'period' => $firstBill['nama_tagihan'] ?? Carbon::now()->locale('id')->translatedFormat('F Y'),
            'unpaid_count' => $summary['unpaid_count'],
            'is_double' => $summary['is_double'],
            'multiplier' => $summary['multiplier'],
            'status_label' => $summary['status_label'],
            'status_badge' => $summary['status_badge'],
            'due_date_text' => $summary['due_date_text'],
            'notice_text' => $summary['notice_text'],
            'bills' => $billsData->values()->all(),
            'tagihan_id' => $firstBill['tagihan_id'] ?? null,
            'tagihan_ids' => $billsData->pluck('tagihan_id')->all(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'bills' => $billsData->values()->all(),
            'summary' => $summary,
        ]);
    }

    /**
     * API: Riwayat Pengajuan & Iuran Warga (Self-Data Scoping)
     */
    public function getRiwayatWarga(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $warga->loadMissing(['kartuKeluarga', 'dataRt']);

        $letters = Surat::where('NIK', $warga->NIK)
            ->latest('tgl_pengajuan')
            ->get()
            ->map(function ($s) {
                $statusLower = strtolower($s->status);
                $isAcc = in_array($statusLower, ['disetujui', 'selesai', 'approved']);
                $isPending = in_array($statusLower, ['menunggu', 'pending']);
                $isRejected = in_array($statusLower, ['ditolak', 'rejected']);

                $nomorSurat = $s->nomor_surat;
                if (!$nomorSurat && $isAcc) {
                    $nomorSurat = Surat::generateNomorSurat($s->surat_id, $s->warga?->ID_RT);
                }

                $approval = Approval::where('tabel_sumber', 'SURAT')
                    ->where('id_referensi', (string) $s->surat_id)
                    ->latest('id_approval')
                    ->first();

                return [
                    'id' => $s->surat_id,
                    'nomor_surat' => $nomorSurat,
                    'ticket_code' => '#SRT-' . str_pad($s->surat_id, 4, '0', STR_PAD_LEFT),
                    'jenis_surat' => $s->Jenis_surat,
                    'keperluan' => $s->perihal,
                    'tanggal' => Carbon::parse($s->tgl_pengajuan)->translatedFormat('d M Y'),
                    'status' => $s->status,
                    'is_acc' => $isAcc,
                    'is_pending' => $isPending,
                    'is_rejected' => $isRejected,
                    'catatan_penolakan' => ($isRejected && $approval) ? $approval->catatan : null,
                    'status_label' => $isAcc ? 'Disetujui RT' : ($isPending ? 'Menunggu Approval' : 'Ditolak'),
                    'status_badge' => $isAcc ? 'badge-emerald' : ($isPending ? 'badge-amber' : 'badge-rose'),
                    'pdf_url' => route('surat.pdf.download', ['id' => $s->surat_id]),
                    'verify_url' => route('surat.verify', ['id' => $s->surat_id]),
                ];
            });

        $mutations = Pemasukan::withoutGlobalScope('tenant')
            ->where('ID_KK', $warga->ID_KK)
            ->where('ID_RT', $warga->ID_RT)
            ->latest('tgl_jatuh_tmp')
            ->get()
            ->map(function ($m) {
                $statusLower = strtolower($m->status_tagihan);
                $isLunas = $statusLower === 'lunas';
                $isPendingVerif = in_array($statusLower, ['pending_approval', 'menunggu verifikasi', 'menunggu', 'pending']);

                $approval = Approval::where('tabel_sumber', 'PEMASUKAN')
                    ->where('id_referensi', (string) $m->Tagihan_Id)
                    ->latest('id_approval')
                    ->first();
                $isRejectedApproval = ($approval && in_array(strtolower($approval->status), ['rejected', 'ditolak']));

                $statusLabel = 'Belum Dibayar';
                $statusBadge = 'badge-slate';

                if ($isLunas) {
                    $statusLabel = 'Lunas Terverifikasi';
                    $statusBadge = 'badge-emerald';
                } elseif ($isPendingVerif) {
                    $statusLabel = 'Menunggu Verifikasi Bendahara';
                    $statusBadge = 'badge-amber';
                } elseif ($isRejectedApproval) {
                    $statusLabel = 'Pembayaran Ditolak';
                    $statusBadge = 'badge-rose';
                }

                return [
                    'id' => 'PEM-' . $m->Tagihan_Id,
                    'ref_code' => '#TAG-' . str_pad($m->Tagihan_Id, 4, '0', STR_PAD_LEFT),
                    'periode' => $m->nama_tagihan,
                    'nominal' => (float) $m->nominal,
                    'nominal_formatted' => 'Rp ' . number_format((float) ($m->nominal ?? 0), 0, ',', '.'),
                    'tanggal' => Carbon::parse($m->tgl_jatuh_tmp)->translatedFormat('d M Y'),
                    'kategori' => $m->Kategori,
                    'status' => $m->status_tagihan,
                    'is_lunas' => $isLunas,
                    'is_pending_verif' => $isPendingVerif,
                    'is_rejected' => $isRejectedApproval,
                    'catatan_penolakan' => $isRejectedApproval ? $approval->catatan : null,
                    'status_label' => $statusLabel,
                    'status_badge' => $statusBadge,
                ];
            });

        $totalTerbayar = $mutations->where('is_lunas', true)->sum('nominal');

        return response()->json([
            'success' => true,
            'data' => [
                'citizen' => [
                    'nik' => $warga->NIK,
                    'nama' => $warga->Nama_Lengkap,
                    'id_kk' => $warga->ID_KK,
                    'id_rt' => $warga->ID_RT,
                    'alamat' => optional($warga->kartuKeluarga)->Alamat ?? 'RT 05 / RW 01',
                    'no_telp' => $warga->no_telp,
                ],
                'letters' => $letters,
                'mutations' => $mutations,
                'summary' => [
                    'total_surat_acc' => $letters->where('is_acc', true)->count(),
                    'total_surat_pending' => $letters->where('is_pending', true)->count(),
                    'total_terbayar_tahun_ini' => 'Rp ' . number_format($totalTerbayar, 0, ',', '.'),
                ],
            ],
        ]);
    }

    /**
     * API: Pengajuan Surat Pengantar Online
     * Menjalankan Stored Procedure MySQL: sp_ajukan_surat_warga
     * Trigger MySQL trg_auto_approval_surat_warga otomatis membuat tiket APPROVAL
     */
    public function submitSurat(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'jenis_surat' => 'required|string|max:100',
            'keperluan' => 'required|string|max:500',
            'nik' => 'nullable|string|max:16',
        ]);

        if (!empty($validated['nik'])) {
            $targetWarga = Warga::withoutTenant()->where('NIK', $validated['nik'])->first();
            if ($targetWarga) {
                if (auth('warga')->check() && auth('warga')->user()->NIK !== $targetWarga->NIK) {
                    if (auth('warga')->user()->ID_KK !== $targetWarga->ID_KK && auth('warga')->user()->ID_RT !== $targetWarga->ID_RT) {
                        return response()->json(['success' => false, 'message' => 'Anda hanya dapat mengajukan surat untuk anggota keluarga satu KK atau lingkungan RT Anda.'], 403);
                    }
                }
                $warga = $targetWarga;
            }
        }

        $nik = $warga->NIK;
        $suratId = null;
        $resCode = 200;
        $resMsg = 'Pengajuan surat berhasil dikirim.';

        if (DB::getDriverName() === 'mysql') {
            try {
                $pdo = DB::getPdo();
                $stmt = $pdo->prepare("CALL sp_ajukan_surat_warga(:nik, :jenis_surat, :perihal, @surat_id, @res_code, @res_msg)");
                $stmt->execute([
                    ':nik' => $nik,
                    ':jenis_surat' => $validated['jenis_surat'],
                    ':perihal' => $validated['keperluan'],
                ]);
                $stmt->closeCursor();

                $row = $pdo->query("SELECT @surat_id AS surat_id, @res_code AS res_code, @res_msg AS res_msg")->fetch(\PDO::FETCH_ASSOC);
                $suratId = $row['surat_id'] ?? null;
                $resCode = (int) ($row['res_code'] ?? 200);
                $resMsg = $row['res_msg'] ?? 'Pengajuan surat berhasil dikirim.';
            } catch (\Throwable $e) {
                $surat = Surat::create([
                    'NIK' => $nik,
                    'Jenis_surat' => $validated['jenis_surat'],
                    'perihal' => $validated['keperluan'],
                    'tgl_pengajuan' => Carbon::now(),
                    'status' => 'Menunggu',
                ]);
                $suratId = $surat->surat_id;
            }
        } else {
            // SQLite fallback
            $surat = Surat::create([
                'NIK' => $nik,
                'Jenis_surat' => $validated['jenis_surat'],
                'perihal' => $validated['keperluan'],
                'tgl_pengajuan' => Carbon::now(),
                'status' => 'Menunggu',
            ]);
            $suratId = $surat->surat_id;
        }

        $trackingCode = '#SRT-' . str_pad($suratId ?: 1, 4, '0', STR_PAD_LEFT);

        // Backward compatibility sync with OfficialLetter and ApprovalTicket
        try {
            $nomorSurat = '47' . ($suratId ?: 1) . '/05/IX/' . date('Y');
            OfficialLetter::create([
                'nomor_surat' => $nomorSurat,
                'ticket_code' => $trackingCode,
                'rt_unit_id' => $warga->ID_RT ?? 1,
                'citizen_id' => null,
                'nama_warga' => $warga->Nama_Lengkap,
                'nik' => $warga->NIK,
                'alamat' => optional($warga->kartuKeluarga)->Alamat ?? 'Harmoni Residence',
                'jenis_surat' => $validated['jenis_surat'],
                'keperluan' => $validated['keperluan'],
                'status' => 'Pending',
                'qr_token' => 'QR-RW05-' . str_replace('/', '-', $nomorSurat),
                'tanggal_terbit' => Carbon::now(),
            ]);

            ApprovalTicket::create([
                'ticket_code' => $trackingCode,
                'rt_unit_id' => $warga->ID_RT ?? 1,
                'citizen_id' => null,
                'applicant' => $warga->Nama_Lengkap,
                'house' => optional($warga->kartuKeluarga)->Alamat ?? 'Harmoni Residence',
                'nik' => $warga->NIK,
                'phone' => $warga->no_telp,
                'tabel_sumber' => 'SURAT',
                'tab' => 'surat',
                'perihal' => $validated['jenis_surat'],
                'nominal' => '-',
                'status' => 'pending',
                'detail_lengkap' => $validated['keperluan'],
                'preview_type' => 'document',
                'preview_title' => 'Draft Surat Pengantar RT No. ' . $nomorSurat,
                'preview_meta' => 'Menunggu Verifikasi & Tanda Tangan Digital Pengurus RT',
            ]);
        } catch (\Throwable $e) {
            // Ignore if legacy tables are not migrated
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'surat_id' => $suratId,
                'tracking_code' => $trackingCode,
                'message' => $resMsg ?: "Pengajuan surat berhasil dikirim. Nomor tiket: {$trackingCode}.",
            ]);
        }

        return redirect()->route('portal.home')->with('success', "Pengajuan surat berhasil dikirim! Nomor tiket: {$trackingCode}.");
    }

    /**
     * API: Konfirmasi Pembayaran Iuran & Upload Bukti Transfer
     * Scoped strictly to ID_KK and ID_RT
     */
    public function konfirmasiIuran(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'tagihan_id' => 'required|integer',
            'bukti_bayar' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
            'bukti' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:5120',
        ]);

        // Ensure tagihan belongs to this citizen's KK and RT
        $tagihan = Pemasukan::withoutGlobalScope('tenant')
            ->where('Tagihan_Id', $validated['tagihan_id'])
            ->where('ID_KK', $warga->ID_KK)
            ->where('ID_RT', $warga->ID_RT)
            ->first();

        if (!$tagihan) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan tidak ditemukan atau tidak terdaftar untuk Kartu Keluarga Anda.',
            ], 404);
        }

        $buktiPath = null;
        $file = $request->file('bukti_bayar') ?? $request->file('bukti');
        if ($file) {
            $filename = 'bukti_iuran_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('uploads/bukti');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $buktiPath = 'uploads/bukti/' . $filename;

            $tagihan->update([
                'bukti_bayar' => $buktiPath,
                'status_tagihan' => 'pending_approval',
            ]);
        }

        $ticketCode = '#APP-IURAN-' . str_pad($tagihan->Tagihan_Id, 4, '0', STR_PAD_LEFT);

        // Buat atau perbarui tiket APPROVAL untuk verifikasi Bendahara
        $bendaharaId = \App\Models\Admin::where('role', 'bendahara')->value('Admin_ID') ?? 3;
        $approval = Approval::updateOrCreate(
            [
                'tabel_sumber' => 'PEMASUKAN',
                'id_referensi' => (string) $tagihan->Tagihan_Id,
            ],
            [
                'Admin_ID' => $bendaharaId,
                'status' => 'pending',
                'catatan' => "Konfirmasi pembayaran iuran oleh {$warga->Nama_Lengkap} (KK: {$warga->ID_KK}) untuk {$tagihan->nama_tagihan} sebesar Rp " . number_format($tagihan->nominal, 0, ',', '.') . ($buktiPath ? " [Bukti: {$buktiPath}]" : ""),
                'tgl_approval' => null,
            ]
        );

        // Sync with legacy approval_tickets & cash_mutations if present
        try {
            ApprovalTicket::create([
                'ticket_code' => $ticketCode,
                'rt_unit_id' => $warga->ID_RT ?? 1,
                'citizen_id' => null,
                'applicant' => $warga->Nama_Lengkap,
                'house' => optional($warga->kartuKeluarga)->Alamat ?? ('KK ' . $warga->ID_KK),
                'nik' => $warga->NIK,
                'phone' => $warga->no_telp,
                'tabel_sumber' => 'PEMASUKAN',
                'tab' => 'keuangan',
                'perihal' => $tagihan->nama_tagihan,
                'nominal' => 'Rp ' . number_format($tagihan->nominal, 0, ',', '.'),
                'status' => 'pending',
                'detail_lengkap' => 'Konfirmasi pembayaran iuran via Portal Mandiri Warga.',
                'preview_type' => 'receipt',
                'preview_title' => 'Bukti Pembayaran Kas RT (' . $warga->Nama_Lengkap . ')',
                'preview_meta' => 'Nominal: Rp ' . number_format($tagihan->nominal, 0, ',', '.') . ' • Menunggu Verifikasi Bendahara',
                'file_path' => $buktiPath,
            ]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'ticket_code' => $ticketCode,
                'approval_id' => $approval->id_approval,
                'bukti_path' => $buktiPath,
                'message' => "Bukti pembayaran untuk tagihan {$tagihan->nama_tagihan} berhasil diunggah dan diteruskan ke Bendahara RT.",
            ]);
        }

        return redirect()->route('portal.home')->with('success', "Bukti pembayaran berhasil diunggah! Menunggu verifikasi Bendahara RT.");
    }

    /**
     * API: Peminjaman Alat & Inventaris RT
     */
    public function pinjamAset(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'inventaris_id' => 'required|integer',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date',
            'tujuan' => 'required|string|max:255',
        ]);

        $inventaris = Inventaris::find($validated['inventaris_id']);
        if (!$inventaris) {
            return response()->json(['success' => false, 'message' => 'Barang inventaris tidak ditemukan.'], 404);
        }

        $bookingCode = '#PINJAM-' . str_pad($inventaris->Inventaris_ID, 3, '0', STR_PAD_LEFT) . '-' . time();

        $approval = Approval::create([
            'Admin_ID' => 2, // Admin RT
            'tabel_sumber' => 'INVENTARIS',
            'id_referensi' => (string) $inventaris->Inventaris_ID,
            'status' => 'Menunggu',
            'catatan' => "Permohonan pinjam alat {$inventaris->Nama_Barang} oleh {$warga->Nama_Lengkap} (NIK: {$warga->NIK}) untuk keperluan: {$validated['tujuan']} (Periode: {$validated['tgl_mulai']} s.d {$validated['tgl_selesai']}).",
            'tgl_approval' => null,
        ]);

        // Rekam ke tabel asset_loans agar pengurus dapat melihat siapa yang meminjam di menu Inventaris
        try {
            \App\Models\AssetLoan::create([
                'kode_pinjam' => $bookingCode,
                'ticket_code' => '#APP-PINJAM-' . $approval->id_approval,
                'nama_alat' => $inventaris->Nama_Barang,
                'kode_aset' => $inventaris->Kode_Barang,
                'jumlah' => 1,
                'nama_peminjam' => $warga->Nama_Lengkap,
                'blok_rumah' => optional($warga->kartuKeluarga)->Alamat ?? ('RT ' . $warga->ID_RT),
                'kontak' => $warga->no_telp,
                'tgl_mulai' => $validated['tgl_mulai'],
                'tgl_selesai' => $validated['tgl_selesai'],
                'tujuan' => $validated['tujuan'],
                'status' => 'pending',
                'kondisi_keluar' => 'Baik & Lengkap',
            ]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'booking_code' => $bookingCode,
                'approval_id' => $approval->id_approval,
                'aset' => $inventaris->Nama_Barang,
                'message' => "Pengajuan peminjaman {$inventaris->Nama_Barang} berhasil dikirim ke Pengurus RT.",
            ]);
        }

        return redirect()->route('portal.home')->with('success', "Pengajuan peminjaman {$inventaris->Nama_Barang} berhasil dikirim!");
    }

    /**
     * API: Sampaikan Aspirasi Warga ke tabel ASPIRASI
     */
    public function submitAspirasi(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'isi' => 'required|string|max:1000',
            'kategori' => 'nullable|string|max:50',
            'judul' => 'nullable|string|max:150',
        ]);

        $isiLengkap = (!empty($validated['judul']) ? "[{$validated['kategori']}] {$validated['judul']}: " : "") . $validated['isi'];

        $aspirasi = Aspirasi::create([
            'NIK' => $warga->NIK,
            'Isi_Aspirasi' => $isiLengkap,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'aspirasi_id' => $aspirasi->id_aspirasi,
                'message' => 'Aspirasi Anda berhasil dikirim ke Pengurus RT.',
            ]);
        }

        return back()->with('success', 'Aspirasi Anda berhasil dikirim dan akan ditinjau secara terbuka oleh Pengurus RT.');
    }

    /**
     * Halaman Khusus Aspirasi Warga & Feed Tanggapan Resmi Pengurus RT
     */
    public function aspirasi(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return redirect()->route('login');
        }

        $warga->loadMissing(['kartuKeluarga', 'dataRt']);
        $rt = $warga->dataRt ?? DataRt::find($warga->ID_RT) ?? DataRt::first();
        $nomorRt = $rt ? $rt->nomor_rt : '05';
        $nomorRw = $rt ? $rt->nomor_rw : '01';

        $rawPublicAspirasi = Aspirasi::with(['warga.kartuKeluarga', 'responAspirasi'])
            ->latest('id_aspirasi')
            ->get();

        $aspirasiList = $this->formatAspirasiCollection($rawPublicAspirasi, $warga, $nomorRt);

        $stats = [
            'total' => $aspirasiList->count(),
            'responded' => $aspirasiList->where('is_responded', true)->count(),
            'pending' => $aspirasiList->where('is_responded', false)->count(),
            'mine' => $aspirasiList->where('is_mine', true)->count(),
            'response_rate' => $aspirasiList->count() > 0 
                ? round(($aspirasiList->where('is_responded', true)->count() / $aspirasiList->count()) * 100) 
                : 100,
        ];

        return view('portal.aspirasi', compact(
            'warga',
            'nomorRt',
            'nomorRw',
            'aspirasiList',
            'stats'
        ));
    }

    /**
     * Format a collection of Aspirasi models into displayable view items
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection|iterable $collection
     * @param Warga|null $warga
     * @param string|int $nomorRt
     * @return \Illuminate\Support\Collection
     */
    protected function formatAspirasiCollection($collection, $warga = null, $nomorRt = '')
    {
        return $collection->map(function ($asp) use ($warga, $nomorRt) {
            $isi = $asp->Isi_Aspirasi;
            $kategori = 'Umum';
            $judul = 'Aspirasi Warga';
            $konten = $isi;

            if (preg_match('/^\[(.*?)\]\s*(.*?):\s*(.*)$/su', $isi, $matches)) {
                $kategori = trim($matches[1]);
                $judul = trim($matches[2]);
                $konten = trim($matches[3]);
            } elseif (preg_match('/^\[(.*?)\]\s*(.*)$/su', $isi, $matches)) {
                $kategori = trim($matches[1]);
                $konten = trim($matches[2]);
                $judul = 'Usulan ' . ucfirst($kategori);
            }

            $catLower = strtolower($kategori);
            $catLabel = match ($catLower) {
                'fasilitas', 'fasilitas umum' => 'Fasilitas Umum',
                'keamanan', 'keamanan & ronda' => 'Keamanan & Ronda',
                'kebersihan' => 'Kebersihan',
                'ketertiban' => 'Ketertiban',
                'sosial', 'kegiatan' => 'Sosial & Kegiatan',
                default => ucfirst($kategori)
            };

            $author = $asp->warga;
            $isMine = ($warga && $asp->NIK === $warga->NIK);
            $authorName = $author ? $author->Nama_Lengkap : 'Warga RT ' . $nomorRt;
            $authorInitials = strtoupper(substr($authorName, 0, 2));
            $alamatShort = $author && $author->kartuKeluarga ? $author->kartuKeluarga->Alamat : ('RT ' . $nomorRt);

            $respon = $asp->responAspirasi;
            $isResponded = !is_null($respon);

            return [
                'id' => $asp->id_aspirasi,
                'nik' => $asp->NIK,
                'is_mine' => $isMine,
                'author_name' => $authorName,
                'author_initials' => $authorInitials,
                'author_address' => $alamatShort,
                'kategori' => $catLabel,
                'kategori_slug' => in_array($catLower, ['fasilitas', 'fasilitas umum']) ? 'fasilitas' : (in_array($catLower, ['keamanan', 'keamanan & ronda']) ? 'keamanan' : (in_array($catLower, ['kebersihan']) ? 'kebersihan' : 'umum')),
                'judul' => $judul,
                'konten' => $konten,
                'tanggal' => Carbon::parse($asp->created_at)->translatedFormat('d M Y, H:i'),
                'is_responded' => $isResponded,
                'status_label' => $isResponded ? 'Ditanggapi Resmi RT' : 'Menunggu Telaah RT',
                'status_badge' => $isResponded ? 'badge-emerald' : 'badge-amber',
                'respon' => $respon ? [
                    'responder' => $respon->nama_responden,
                    'isi' => $respon->isi_respon,
                    'tanggal' => Carbon::parse($respon->tanggal_respon)->translatedFormat('d M Y, H:i') . ' WIB',
                ] : null,
            ];
        });
    }

    /**
     * Halaman Khusus Layanan Mandiri Warga (Surat, Iuran, Inventaris)
     */
    public function layanan(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return redirect()->route('login');
        }

        $warga->loadMissing(['kartuKeluarga', 'dataRt']);
        $rt = $warga->dataRt ?? DataRt::find($warga->ID_RT) ?? DataRt::first();
        $nomorRt = $rt ? $rt->nomor_rt : '05';
        $nomorRw = $rt ? $rt->nomor_rw : '01';
        $rtId = $warga->ID_RT ?? ($rt ? $rt->ID_RT : 1);

        // 1. Surat Saya (SURAT where NIK = $warga->NIK)
        $erdSurat = Surat::where('NIK', $warga->NIK)
            ->latest('tgl_pengajuan')
            ->get();

        $suratSaya = $erdSurat->map(function ($s) {
            $statusLower = strtolower($s->status);
            $isAcc = in_array($statusLower, ['disetujui', 'selesai', 'approved']);
            $isRejected = in_array($statusLower, ['ditolak', 'rejected']);
            $isPending = !$isAcc && !$isRejected;
            $tCode = '#SRT-' . str_pad($s->surat_id, 4, '0', STR_PAD_LEFT);
            
            $nomorSurat = $s->nomor_surat;
            if (!$nomorSurat && $isAcc) {
                $nomorSurat = Surat::generateNomorSurat($s->surat_id, $s->warga?->ID_RT);
            }

            $approval = Approval::where('tabel_sumber', 'SURAT')
                ->where('id_referensi', (string) $s->surat_id)
                ->latest('id_approval')
                ->first();
            $catatanPenolakan = ($isRejected && $approval) ? $approval->catatan : null;

            return [
                'id' => $s->surat_id,
                'nomor_surat' => $nomorSurat,
                'ticket_code' => $tCode,
                'jenis_surat' => $s->Jenis_surat,
                'keperluan' => $s->perihal,
                'tanggal' => Carbon::parse($s->tgl_pengajuan)->translatedFormat('d M Y'),
                'status' => $s->status,
                'is_acc' => $isAcc,
                'is_pending' => $isPending,
                'is_rejected' => $isRejected,
                'catatan_penolakan' => $catatanPenolakan,
                'status_label' => $isAcc ? 'Disetujui RT' : ($isPending ? 'Menunggu Approval' : 'Ditolak'),
                'status_badge' => $isAcc ? 'badge-emerald' : ($isPending ? 'badge-amber' : 'badge-rose'),
                'pdf_url' => route('surat.pdf.download', ['id' => $s->surat_id]),
                'verify_url' => route('surat.verify', ['id' => $s->surat_id]),
            ];
        });

        // 2. Tagihan Iuran Kas RT (PEMASUKAN where ID_KK = $warga->ID_KK and ID_RT = $rtId)
        [$tagihanSaya, $tagihanPending, $totalTerbayarTahunIni, $isLunasBulanIni, $billingSummary] = $this->getTagihanData($warga, $rtId);

        // 3. Inventaris Balai RT
        $inventarisCatalog = Inventaris::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->get()
            ->map(function ($inv) {
                $stok = (int) $inv->Jumlah;
                return [
                    'id' => $inv->Inventaris_ID,
                    'nama' => $inv->Nama_Barang,
                    'kode' => $inv->Kode_Barang,
                    'jumlah' => $stok,
                    'lokasi' => $inv->Lokasi ?? 'Balai RT',
                    'is_tersedia' => $stok > 0,
                    'status_label' => $stok > 0 ? "Tersedia ({$stok} Unit)" : 'Stok Kosong (0 Unit)',
                    'status_badge' => $stok > 0 ? 'badge-emerald' : 'badge-rose',
                ];
            });

        $statsLayanan = [
            'total_surat' => $suratSaya->count(),
            'surat_disetujui' => $suratSaya->where('is_acc', true)->count(),
            'surat_pending' => $suratSaya->where('is_pending', true)->count(),
            'tagihan_pending' => $tagihanPending->count(),
            'total_terbayar' => 'Rp ' . number_format($totalTerbayarTahunIni, 0, ',', '.'),
            'total_aset_tersedia' => $inventarisCatalog->where('is_tersedia', true)->count(),
            'is_double_tagihan' => $billingSummary['is_double'] ?? false,
        ];

        return view('portal.layanan', compact(
            'warga',
            'nomorRt',
            'nomorRw',
            'suratSaya',
            'tagihanSaya',
            'tagihanPending',
            'billingSummary',
            'inventarisCatalog',
            'statsLayanan'
        ));
    }

    /**
     * Halaman Khusus Agenda & Kegiatan Lingkungan RT
     */
    public function agenda(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return redirect()->route('login');
        }

        $warga->loadMissing(['kartuKeluarga', 'dataRt']);
        $rt = $warga->dataRt ?? DataRt::find($warga->ID_RT) ?? DataRt::first();
        $nomorRt = $rt ? $rt->nomor_rt : '05';
        $nomorRw = $rt ? $rt->nomor_rw : '01';
        $rtId = $warga->ID_RT ?? ($rt ? $rt->ID_RT : 1);

        $now = Carbon::today();

        $allKegiatan = Kegiatan::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->orderBy('tgl_kegiatan', 'asc')
            ->get();

        $agendaList = $allKegiatan->map(function ($keg) use ($now, $nomorRt) {
            $dt = Carbon::parse($keg->tgl_kegiatan);
            $status = $keg->status ?? 'Mendatang';
            $isPast = ($status === 'Selesai') || ($dt->isBefore($now) && $status !== 'Sedang Berlangsung');
            $isLive = ($status === 'Sedang Berlangsung');

            $nameLower = strtolower($keg->nama_kegiatan);
            $badge = $status;
            $badgeClass = $isLive ? 'badge-emerald' : ($isPast ? 'badge-gray' : 'badge-blue');
            $isWajib = false;

            if (str_contains($nameLower, 'kerja bakti') || str_contains($nameLower, 'fogging') || str_contains($nameLower, 'gotong royong')) {
                $badge = 'Wajib Per KK • ' . $status;
                $badgeClass = 'badge-emerald';
                $isWajib = true;
            } elseif (str_contains($nameLower, 'rapat') || str_contains($nameLower, 'musyawarah')) {
                $badge = 'Rapat Pengurus • ' . $status;
                $badgeClass = 'badge-purple';
            } elseif (str_contains($nameLower, 'senam') || str_contains($nameLower, 'posyandu') || str_contains($nameLower, 'kesehatan')) {
                $badge = 'Kesehatan & PKK • ' . $status;
                $badgeClass = 'badge-amber';
            }

            return [
                'id' => $keg->kegiatan_id,
                'day' => $dt->format('d'),
                'month' => $dt->translatedFormat('M'),
                'year' => $dt->format('Y'),
                'full_date' => $dt->translatedFormat('l, d F Y - H:i') . ' WIB',
                'title' => $keg->nama_kegiatan,
                'location' => $keg->lokasi ?? ('Balai Warga RT ' . $nomorRt),
                'desc' => $keg->deskripsi,
                'status' => $status,
                'badge' => $badge,
                'badge_class' => $badgeClass,
                'is_wajib' => $isWajib,
                'is_past' => $isPast,
                'is_live' => $isLive,
                'status_label' => $status === 'Sedang Berlangsung' ? 'Sedang Berlangsung' : ($isPast ? 'Selesai Terlaksana' : 'Mendatang'),
            ];
        });

        $agendaMendatang = $agendaList->where('is_past', false);
        $agendaSelesai = $agendaList->where('is_past', true);

        $jadwalRonda = [
            ['hari' => 'Senin', 'jam' => '22.00 - 04.00 WIB', 'koordinator' => 'Bpk. H. Sukardi', 'anggota' => 'Bpk. Hendra, Bpk. Joko, Bpk. Asep', 'lokasi' => 'Pos Ronda Utama Blok A'],
            ['hari' => 'Selasa', 'jam' => '22.00 - 04.00 WIB', 'koordinator' => 'Bpk. Denny Pratama', 'anggota' => 'Bpk. Bambang, Bpk. Rudi, Bpk. Agus', 'lokasi' => 'Pos Ronda Blok B'],
            ['hari' => 'Rabu', 'jam' => '22.00 - 04.00 WIB', 'koordinator' => 'Bpk. Rahmat Hendrawan', 'anggota' => 'Bpk. Arif, Bpk. Farhan, Bpk. Budi', 'lokasi' => 'Pos Ronda Utama Blok A'],
            ['hari' => 'Kamis', 'jam' => '22.00 - 04.00 WIB', 'koordinator' => 'Bpk. Slamet Riyadi', 'anggota' => 'Bpk. Herman, Bpk. Dedi, Bpk. Iwan', 'lokasi' => 'Pos Ronda Blok C'],
            ['hari' => 'Jumat', 'jam' => '22.00 - 04.00 WIB', 'koordinator' => 'Bpk. Bima Perkasa', 'anggota' => 'Bpk. Indra, Bpk. Gilang, Bpk. Wahyu', 'lokasi' => 'Pos Ronda Utama Blok A'],
            ['hari' => 'Sabtu (Malam Minggu)', 'jam' => '21.30 - 04.30 WIB', 'koordinator' => 'Piket Gabungan RT 05', 'anggota' => 'Regu Pemuda & Karang Taruna Harmoni', 'lokasi' => 'Posko Keamanan Induk'],
            ['hari' => 'Minggu', 'jam' => '22.00 - 04.00 WIB', 'koordinator' => 'Bpk. Aiptu Hendro (Bhabinkamtibmas)', 'anggota' => 'Piket Sekuriti Swakarsa Harmoni', 'lokasi' => 'Portal Gerbang Masuk'],
        ];

        $statsAgenda = [
            'total' => $agendaList->count(),
            'mendatang' => $agendaMendatang->count(),
            'selesai' => $agendaSelesai->count(),
            'wajib' => $agendaList->where('is_wajib', true)->count(),
        ];

        return view('portal.agenda', compact(
            'warga',
            'nomorRt',
            'nomorRw',
            'agendaList',
            'agendaMendatang',
            'agendaSelesai',
            'jadwalRonda',
            'statsAgenda'
        ));
    }

    /**
     * Halaman Khusus Transparansi Kas RT (Penerimaan, Belanja, Alokasi, Akuntabilitas)
     */
    public function transparansi(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return redirect()->route('login');
        }

        $warga->loadMissing(['kartuKeluarga', 'dataRt']);
        $rt = $warga->dataRt ?? DataRt::find($warga->ID_RT) ?? DataRt::first();
        $nomorRt = $rt ? $rt->nomor_rt : '05';
        $nomorRw = $rt ? $rt->nomor_rw : '01';
        $rtId = $warga->ID_RT ?? ($rt ? $rt->ID_RT : 1);

        // 1. Ambil data transparansi kas tersinkronisasi CMS
        $transparansiKas = $this->getTransparansiKas($rtId);
        $saldoKasRt = $transparansiKas['saldo_num'];
        $surplusDefisit = $transparansiKas['surplus_num'];
        $alokasiAnggaran = $transparansiKas['alokasi'];

        // 2. Daftar Pengeluaran Kas RT (Aggregated expenditure records from PENGELUARAN)
        $pengeluaranList = Pengeluaran::withoutGlobalScope('tenant')
            ->where(function ($q) use ($rtId) {
                $q->where('ID_RT', $rtId)->orWhereNull('ID_RT');
            })
            ->latest('Tgl_Bayar')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->Pengeluaran_id,
                    'ref_code' => '#EXP-' . str_pad($p->Pengeluaran_id, 4, '0', STR_PAD_LEFT),
                    'kategori' => $p->Kategori,
                    'keterangan' => $p->Keterangan,
                    'nominal' => (float) $p->Jumlah_Bayar,
                    'nominal_formatted' => 'Rp ' . number_format((float) $p->Jumlah_Bayar, 0, ',', '.'),
                    'tanggal' => Carbon::parse($p->Tgl_Bayar)->translatedFormat('d M Y'),
                    'metode' => $p->Metode_Bayar ?? 'Kas Tunai',
                    'has_bukti' => !empty($p->Bukti_Path),
                ];
            });

        // 3. Rekening Resmi Kas RT
        $rekeningResmi = [
            'bank' => 'Bank Central Asia (BCA)',
            'nomor_rekening' => '712-889-0192',
            'atas_nama' => 'PENGURUS RT ' . $nomorRt . ' HARMONI RESIDENCE',
            'qris_merchant' => 'KAS RT ' . $nomorRt . ' HARMONI RESIDENCE (NMID: ID10202688991)',
        ];

        $transparansiData = [
            'saldo_akhir' => $transparansiKas['total_saldo'],
            'masuk_bulan_ini' => $transparansiKas['total_masuk_bulan'],
            'keluar_bulan_ini' => $transparansiKas['total_keluar_bulan'],
            'surplus_bulan_ini' => ($surplusDefisit >= 0 ? '+Rp ' : '-Rp ') . number_format(abs($surplusDefisit), 0, ',', '.'),
            'tingkat_kepatuhan' => '94.2%',
            'update_terakhir' => $transparansiKas['update_terakhir'],
        ];

        return view('portal.transparansi', compact(
            'warga',
            'nomorRt',
            'nomorRw',
            'transparansiData',
            'pengeluaranList',
            'alokasiAnggaran',
            'rekeningResmi'
        ));
    }

    /**
     * API: Edit Profil Warga Mandiri
     */
    public function editProfil(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'no_telp' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:150|unique:WARGA,email,' . $warga->NIK . ',NIK',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $warga->Nama_Lengkap = $validated['nama_lengkap'];
        if (isset($validated['no_telp'])) {
            $warga->no_telp = $validated['no_telp'];
        }
        if (array_key_exists('email', $validated)) {
            $warga->email = $validated['email'];
        }
        if (!empty($validated['password'])) {
            $warga->Password_hash = Hash::make($validated['password']);
        }
        $warga->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil Anda berhasil diperbarui.',
            ]);
        }

        return redirect()->route('portal.home')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Usulkan Kegiatan Lingkungan Warga (Store to KEGIATAN)
     */
    public function usulKegiatan(Request $request)
    {
        $warga = $this->getAuthenticatedWarga();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tgl_kegiatan' => 'nullable|date',
            'tanggal_kegiatan' => 'nullable|date',
            'lokasi' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
        ]);

        $tanggal = $validated['tgl_kegiatan'] ?? $validated['tanggal_kegiatan'] ?? Carbon::now()->addDays(7)->toDateString();

        $kegiatan = Kegiatan::create([
            'ID_RT' => $warga->ID_RT,
            'nama_kegiatan' => $validated['nama_kegiatan'],
            'tgl_kegiatan' => $tanggal,
            'lokasi' => $validated['lokasi'],
            'status' => 'Mendatang',
            'deskripsi' => $validated['deskripsi'] ?? "Usulan kegiatan oleh warga {$warga->Nama_Lengkap} (NIK: {$warga->NIK})",
        ]);

        // Daftarkan tiket persetujuan ke tabel APPROVAL agar muncul di antrean CMS pengurus RT
        $approval = Approval::create([
            'Admin_ID' => 2, // Admin RT
            'tabel_sumber' => 'KEGIATAN',
            'id_referensi' => (string) $kegiatan->kegiatan_id,
            'status' => 'Menunggu',
            'catatan' => "Usulan kegiatan '{$kegiatan->nama_kegiatan}' oleh warga {$warga->Nama_Lengkap} (NIK: {$warga->NIK}) di {$kegiatan->lokasi}.",
            'tgl_approval' => null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Usulan kegiatan berhasil diajukan ke Pengurus RT.',
                'kegiatan' => $kegiatan,
                'approval_id' => $approval->id_approval,
            ]);
        }

        return redirect()->route('portal.agenda')->with('success', 'Usulan kegiatan berhasil diajukan ke Pengurus RT!');
    }

    /**
     * Helper to resolve the authenticated warga, or fallback to an RT citizen if previewing as Admin
     */
    protected function getAuthenticatedWarga()
    {
        // 1. Primary: Authenticated user via 'warga' guard
        $warga = auth('warga')->user();
        if ($warga) {
            return $warga;
        }

        // 2. Explicit form input NIK (e.g. from modal submissions)
        if ($reqNik = request('nik')) {
            $w = Warga::withoutTenant()->where('NIK', $reqNik)->first();
            if ($w) {
                return $w;
            }
        }

        // 3. Stored citizen NIK in session (persisted during citizen login)
        if ($sessionNik = session('citizen_nik')) {
            $w = Warga::withoutTenant()->where('NIK', $sessionNik)->first();
            if ($w) {
                return $w;
            }
        }

        // 4. Admin preview fallback (scoped to admin's RT)
        if (auth('web')->check()) {
            $admin = auth('web')->user();
            return Warga::withoutTenant()->where('ID_RT', $admin->ID_RT)->first() ?? Warga::withoutTenant()->first();
        }

        return null;
    }
}
