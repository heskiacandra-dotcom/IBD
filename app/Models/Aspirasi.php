<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    use HasFactory;

    protected $table = 'ASPIRASI';
    protected $primaryKey = 'id_aspirasi';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    public function warga()
    {
        return $this->belongsTo(Warga::class, 'NIK', 'NIK');
    }

    public function responAspirasi()
    {
        return $this->hasOne(ResponAspirasi::class, 'id_aspirasi', 'id_aspirasi');
    }

    public function respon()
    {
        return $this->responAspirasi();
    }
}
