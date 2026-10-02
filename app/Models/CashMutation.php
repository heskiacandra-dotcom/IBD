<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashMutation extends Model
{
    use HasFactory;

    protected $fillable = [
        'rt_unit_id',
        'ticket_code',
        'tipe',
        'kategori',
        'perihal',
        'nominal',
        'nama_pihak',
        'alamat_blok',
        'periode',
        'status',
        'tanggal',
        'bukti_file',
    ];

    protected $casts = [
        'nominal' => 'float',
        'tanggal' => 'date',
    ];

    public function rtUnit()
    {
        return $this->belongsTo(RtUnit::class);
    }
}
