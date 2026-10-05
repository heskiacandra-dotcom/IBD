<?php

namespace App\Models;

use App\Services\WhatsAppGatewayService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'SURAT';
    protected $primaryKey = 'surat_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'tgl_pengajuan' => 'date',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'NIK', 'NIK');
    }

    public function approval()
    {
        return $this->hasOne(Approval::class, 'id_referensi', 'surat_id')
            ->where('tabel_sumber', 'SURAT');
    }

    public function approve(?int $adminId = null): string
    {
        $nomorSurat = $this->nomor_surat ?: self::generateNomorSurat($this->surat_id, $this->warga?->ID_RT);

        $this->update([
            'status' => 'approved',
            'nomor_surat' => $nomorSurat,
        ]);

        Approval::where('tabel_sumber', 'SURAT')
            ->where('id_referensi', (string) $this->surat_id)
            ->update([
                'status' => 'approved',
                'tgl_approval' => now(),
                'Admin_ID' => $adminId ?? auth()->id() ?? 1,
            ]);

        try {
            if ($this->warga) {
                app(WhatsAppGatewayService::class)->notifySuratDisetujui($this->warga, $this);
            }
        } catch (\Throwable $e) {}

        return $nomorSurat;
    }

    public function reject(string $alasan, ?int $adminId = null): void
    {
        $this->update([
            'status' => 'rejected',
        ]);

        $approvals = Approval::where('tabel_sumber', 'SURAT')
            ->where(function ($q) {
                $q->where('id_referensi', (string) $this->surat_id)
                  ->orWhere('id_referensi', $this->surat_id);
            })
            ->get();

        foreach ($approvals as $approval) {
            $catatan = trim(($approval->catatan ? $approval->catatan . ' | ' : '') . 'Alasan Penolakan: ' . $alasan);
            $approval->update([
                'status' => 'rejected',
                'catatan' => $catatan,
                'tgl_approval' => now(),
                'Admin_ID' => $adminId ?? auth()->id() ?? 1,
            ]);
        }
    }

    public static function generateNomorSurat(int $suratId, ?int $idRt = null): string
    {
        $rt = $idRt ? DataRt::find($idRt) : DataRt::first();
        $nomorRt = str_pad($rt?->nomor_rt ?? '05', 2, '0', STR_PAD_LEFT);
        $nomorRw = str_pad($rt?->nomor_rw ?? '01', 2, '0', STR_PAD_LEFT);

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        $romanMonth = $romanMonths[(int) date('n')] ?? 'X';
        $year = date('Y');
        $urut = str_pad($suratId, 3, '0', STR_PAD_LEFT);

        return "{$urut}/SP-RT{$nomorRt}/RW{$nomorRw}/{$romanMonth}/{$year}";
    }
}
