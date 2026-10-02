<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficialLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'ticket_code',
        'rt_unit_id',
        'citizen_id',
        'nama_warga',
        'nik',
        'alamat',
        'jenis_surat',
        'keperluan',
        'status',
        'qr_token',
        'tanggal_terbit',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function rtUnit()
    {
        return $this->belongsTo(RtUnit::class);
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }
}
