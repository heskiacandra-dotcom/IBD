<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataRt extends Model
{
    use HasFactory;

    protected $table = 'DATA_RT';
    protected $primaryKey = 'ID_RT';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    public function admins()
    {
        return $this->hasMany(Admin::class, 'ID_RT', 'ID_RT');
    }

    public function ketuaRtAdmin()
    {
        return $this->hasOne(Admin::class, 'ID_RT', 'ID_RT')->where('role', 'admin_rt');
    }

    public function warga()
    {
        return $this->hasMany(Warga::class, 'ID_RT', 'ID_RT');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'ID_RT', 'ID_RT');
    }

    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class, 'ID_RT', 'ID_RT');
    }

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'ID_RT', 'ID_RT');
    }
}
