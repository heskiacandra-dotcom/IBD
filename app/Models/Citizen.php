<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Citizen extends Model
{
    use HasFactory;

    protected $fillable = [
        'rt_unit_id',
        'nik',
        'no_kk',
        'nama',
        'alamat',
        'whatsapp',
        'status_domisili',
        'is_kepala_keluarga',
        'pekerjaan',
        'gender',
    ];

    protected $casts = [
        'is_kepala_keluarga' => 'boolean',
    ];

    public function rtUnit()
    {
        return $this->belongsTo(RtUnit::class);
    }

    public function tickets()
    {
        return $this->hasMany(ApprovalTicket::class);
    }

    public function letters()
    {
        return $this->hasMany(OfficialLetter::class);
    }
}
