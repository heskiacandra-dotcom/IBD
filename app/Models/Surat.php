<?php

namespace App\Models;

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
