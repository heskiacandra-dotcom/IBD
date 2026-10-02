<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScope;

class Pengeluaran extends Model
{
    use HasFactory, TenantScope;

    protected $table = 'PENGELUARAN';
    protected $primaryKey = 'Pengeluaran_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'Tgl_Bayar' => 'date',
        'Jumlah_Bayar' => 'decimal:2',
    ];

    public function pembelianInventaris()
    {
        return $this->hasMany(PembelianInventaris::class, 'Pengeluaran_id', 'Pengeluaran_id');
    }

    public function pembelian()
    {
        return $this->pembelianInventaris();
    }

    public function dataRt()
    {
        return $this->belongsTo(DataRt::class, 'ID_RT', 'ID_RT');
    }

    public function rt()
    {
        return $this->dataRt();
    }

    public function getBuktiPathUrlAttribute(): ?string
    {
        if (empty($this->Bukti_Path)) {
            return null;
        }

        if (str_starts_with($this->Bukti_Path, 'http://') || str_starts_with($this->Bukti_Path, 'https://')) {
            return $this->Bukti_Path;
        }

        if (file_exists(public_path($this->Bukti_Path))) {
            return asset($this->Bukti_Path);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->Bukti_Path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->Bukti_Path);
        }

        if (file_exists(public_path('storage/' . ltrim($this->Bukti_Path, '/')))) {
            return asset('storage/' . ltrim($this->Bukti_Path, '/'));
        }

        return asset($this->Bukti_Path);
    }

    public function hasValidBukti(): bool
    {
        if (empty($this->Bukti_Path)) {
            return false;
        }

        if (str_starts_with($this->Bukti_Path, 'http://') || str_starts_with($this->Bukti_Path, 'https://')) {
            return true;
        }

        if (file_exists(public_path($this->Bukti_Path))) {
            return true;
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->Bukti_Path)) {
            return true;
        }

        if (file_exists(public_path('storage/' . ltrim($this->Bukti_Path, '/')))) {
            return true;
        }

        return false;
    }
}
