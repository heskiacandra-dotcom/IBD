<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'APPROVAL';
    protected $primaryKey = 'id_approval';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $casts = [
        'tgl_approval' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'Admin_ID', 'Admin_ID');
    }

    public function pemasukan()
    {
        return $this->belongsTo(Pemasukan::class, 'id_referensi', 'Tagihan_Id');
    }

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'id_referensi', 'Pengeluaran_id');
    }

    public function getBuktiUrlAttribute(): ?string
    {
        if ($this->tabel_sumber === 'PEMASUKAN') {
            $pem = Pemasukan::withoutGlobalScope('tenant')->find($this->id_referensi);
            if ($pem && !empty($pem->bukti_bayar)) {
                return $pem->bukti_bayar_url;
            }
            if (preg_match('/File:\s*([^\s]+)/', $this->catatan ?? '', $m)) {
                $path = trim($m[1]);
                if (str_starts_with($path, 'http')) {
                    return $path;
                }
                if (file_exists(public_path($path))) {
                    return asset($path);
                }
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                }
                return asset($path);
            }
        }

        if ($this->tabel_sumber === 'PENGELUARAN') {
            $peng = Pengeluaran::find($this->id_referensi);
            if ($peng && !empty($peng->Bukti_Path)) {
                return $peng->bukti_path_url;
            }
        }

        return null;
    }

    public function hasValidBukti(): bool
    {
        if ($this->tabel_sumber === 'PEMASUKAN') {
            $pem = Pemasukan::withoutGlobalScope('tenant')->find($this->id_referensi);
            if ($pem && $pem->hasValidBuktiBayar()) {
                return true;
            }
            if (preg_match('/File:\s*([^\s]+)/', $this->catatan ?? '', $m)) {
                $path = trim($m[1]);
                if (str_starts_with($path, 'http') || file_exists(public_path($path)) || file_exists(public_path('storage/' . ltrim($path, '/')))) {
                    return true;
                }
            }
        }

        if ($this->tabel_sumber === 'PENGELUARAN') {
            $peng = Pengeluaran::find($this->id_referensi);
            return $peng ? $peng->hasValidBukti() : false;
        }

        return false;
    }

    public function getSourceDetailAttribute(): string
    {
        if ($this->tabel_sumber === 'SURAT') {
            $surat = Surat::with('warga')->find($this->id_referensi);
            return $surat ? ($surat->warga->Nama_Lengkap ?? 'Warga') . ' - ' . $surat->Jenis_surat : 'Surat #' . $this->id_referensi;
        }
        if ($this->tabel_sumber === 'PEMASUKAN') {
            $pem = Pemasukan::withoutGlobalScope('tenant')->with('kartuKeluarga')->find($this->id_referensi);
            return $pem ? $pem->nama_tagihan . ' (Rp ' . number_format((float) $pem->nominal, 0, ',', '.') . ')' : 'Tagihan #' . $this->id_referensi;
        }
        if ($this->tabel_sumber === 'INVENTARIS') {
            $inv = Inventaris::find($this->id_referensi);
            return $inv ? 'Aset: ' . $inv->Nama_Barang : 'Aset #' . $this->id_referensi;
        }
        if ($this->tabel_sumber === 'KEGIATAN') {
            $keg = Kegiatan::find($this->id_referensi);
            return $keg ? 'Kegiatan: ' . $keg->nama_kegiatan : 'Kegiatan #' . $this->id_referensi;
        }
        if ($this->tabel_sumber === 'PENGELUARAN') {
            $out = Pengeluaran::find($this->id_referensi);
            return $out ? $out->Keterangan . ' (Rp ' . number_format((float) $out->Jumlah_Bayar, 0, ',', '.') . ')' : 'Pengeluaran #' . $this->id_referensi;
        }
        return $this->tabel_sumber . ' #' . $this->id_referensi;
    }
}
