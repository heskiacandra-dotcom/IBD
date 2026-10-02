<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponAspirasi extends Model
{
    use HasFactory;

    protected $table = 'RESPON_ASPIRASI';
    protected $primaryKey = 'id_aspirasi';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'tanggal_respon' => 'datetime',
    ];

    public function aspirasi()
    {
        return $this->belongsTo(Aspirasi::class, 'id_aspirasi', 'id_aspirasi');
    }
}
