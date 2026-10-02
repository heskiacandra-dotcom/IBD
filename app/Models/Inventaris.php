<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScope;

class Inventaris extends Model
{
    use HasFactory, TenantScope;

    protected $table = 'INVENTARIS';
    protected $primaryKey = 'Inventaris_ID';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    public function dataRt()
    {
        return $this->belongsTo(DataRt::class, 'ID_RT', 'ID_RT');
    }

    public function rt()
    {
        return $this->dataRt();
    }

    public function pembelianInventaris()
    {
        return $this->hasMany(PembelianInventaris::class, 'inventaris_id', 'Inventaris_ID');
    }

    public function pembelian()
    {
        return $this->pembelianInventaris();
    }
}
