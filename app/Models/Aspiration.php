<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspiration extends Model
{
    use HasFactory;

    protected $fillable = [
        'rt_unit_id',
        'kategori',
        'judul',
        'isi',
        'nama',
        'blok',
        'is_anonim',
        'status',
        'tanggapan_rt',
    ];

    protected $casts = [
        'is_anonim' => 'boolean',
    ];

    public function rtUnit()
    {
        return $this->belongsTo(RtUnit::class);
    }
}
