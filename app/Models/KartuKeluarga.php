<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuKeluarga extends Model
{
    use HasFactory;

    protected $table = 'KARTU_KELUARGA';
    protected $primaryKey = 'ID_KK';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function warga()
    {
        return $this->hasMany(Warga::class, 'ID_KK', 'ID_KK');
    }

    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class, 'ID_KK', 'ID_KK');
    }
}
