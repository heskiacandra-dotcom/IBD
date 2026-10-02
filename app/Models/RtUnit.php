<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RtUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_rt',
        'nama_kawasan',
        'kelurahan',
        'kecamatan',
        'kota',
        'ketua_nama',
        'ketua_kontak',
        'status',
    ];

    public function citizens()
    {
        return $this->hasMany(Citizen::class);
    }

    public function tickets()
    {
        return $this->hasMany(ApprovalTicket::class);
    }

    public function letters()
    {
        return $this->hasMany(OfficialLetter::class);
    }

    public function cashMutations()
    {
        return $this->hasMany(CashMutation::class);
    }
}
