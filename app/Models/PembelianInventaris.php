<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianInventaris extends Model
{
    use HasFactory;

    protected $table = 'PEMBELIAN_INVENTARIS';
    protected $primaryKey = 'Pembelian_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'tgl_pembelian' => 'date',
        'total_biaya' => 'decimal:2',
    ];

    public function inventaris()
    {
        return $this->belongsTo(Inventaris::class, 'inventaris_id', 'Inventaris_ID');
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'Pengeluaran_id', 'Pengeluaran_id');
    }
}
