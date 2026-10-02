<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScope;

class Kegiatan extends Model
{
    use HasFactory, TenantScope;

    protected $table = 'KEGIATAN';
    protected $primaryKey = 'kegiatan_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'tgl_kegiatan' => 'date',
    ];

    public function dataRt()
    {
        return $this->belongsTo(DataRt::class, 'ID_RT', 'ID_RT');
    }

    public function rt()
    {
        return $this->dataRt();
    }
}
