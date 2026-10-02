<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\TenantScope;

class Warga extends Authenticatable
{
    use HasFactory, TenantScope;

    protected $table = 'WARGA';
    protected $primaryKey = 'NIK';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $hidden = [
        'Password_hash',
        'remember_token',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function getAuthIdentifierName()
    {
        return 'NIK';
    }

    public function getAuthIdentifier()
    {
        return $this->NIK;
    }

    public function getAuthPassword()
    {
        return $this->Password_hash;
    }

    public function getAuthPasswordName()
    {
        return 'Password_hash';
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function kartuKeluarga()
    {
        return $this->belongsTo(KartuKeluarga::class, 'ID_KK', 'ID_KK');
    }

    public function dataRt()
    {
        return $this->belongsTo(DataRt::class, 'ID_RT', 'ID_RT');
    }

    public function rt()
    {
        return $this->dataRt();
    }

    public function surat()
    {
        return $this->hasMany(Surat::class, 'NIK', 'NIK');
    }

    public function aspirasi()
    {
        return $this->hasMany(Aspirasi::class, 'NIK', 'NIK');
    }
}
