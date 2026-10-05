<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenantScope;
use Illuminate\Support\Collection;

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

    public function loans()
    {
        return $this->hasMany(AssetLoan::class, 'kode_aset', 'Kode_Barang');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'id_referensi', 'Inventaris_ID')
            ->where('tabel_sumber', 'INVENTARIS');
    }

    /**
     * Mendapatkan daftar terpadu warga yang meminjam atau memiliki riwayat pinjam aset ini
     */
    public function getDaftarPeminjam(): Collection
    {
        $loans = AssetLoan::where('kode_aset', $this->Kode_Barang)
            ->orWhere('nama_alat', $this->Nama_Barang)
            ->latest('tgl_mulai')
            ->get();

        $approvals = Approval::where('tabel_sumber', 'INVENTARIS')
            ->where('id_referensi', (string) $this->Inventaris_ID)
            ->latest('id_approval')
            ->get();

        $merged = collect();

        foreach ($loans as $loan) {
            $statusLower = strtolower($loan->status ?? 'pending');
            $isActive = in_array($statusLower, ['borrowed', 'pending', 'menunggu', 'disetujui']);
            $canReturn = in_array($statusLower, ['borrowed', 'disetujui']);

            $merged->push([
                'id' => 'loan-' . $loan->id,
                'loan_id' => $loan->id,
                'approval_id' => null,
                'nama' => $loan->nama_peminjam ?? 'Warga RT',
                'kontak' => $loan->kontak ?? '-',
                'alamat' => $loan->blok_rumah ?? '-',
                'tgl_mulai' => $loan->tgl_mulai ? \Illuminate\Support\Carbon::parse($loan->tgl_mulai)->translatedFormat('d M Y') : '-',
                'tgl_selesai' => $loan->tgl_selesai ? \Illuminate\Support\Carbon::parse($loan->tgl_selesai)->translatedFormat('d M Y') : '-',
                'tujuan' => $loan->tujuan ?? 'Keperluan warga',
                'status' => $statusLower,
                'status_label' => match ($statusLower) {
                    'borrowed' => 'Sedang Dipinjam',
                    'pending', 'menunggu' => 'Menunggu Konfirmasi',
                    'returned', 'selesai' => 'Sudah Dikembalikan',
                    'rejected', 'ditolak' => 'Ditolak',
                    default => ucfirst($statusLower),
                },
                'status_color' => match ($statusLower) {
                    'borrowed' => 'warning',
                    'pending', 'menunggu' => 'info',
                    'returned', 'selesai' => 'success',
                    'rejected', 'ditolak' => 'danger',
                    default => 'gray',
                },
                'is_active' => $isActive,
                'can_return' => $canReturn,
            ]);
        }

        foreach ($approvals as $app) {
            $alreadyExists = $merged->contains(function ($item) use ($app) {
                return str_contains($app->catatan ?? '', $item['nama']);
            });

            if (!$alreadyExists && !empty($app->catatan)) {
                preg_match('/oleh\s+([^(\[]+)(?:\s*\((?:NIK:\s*)?([^)]*)\))?/i', $app->catatan, $matchNama);
                preg_match('/keperluan:\s*([^(\n.]+)/i', $app->catatan, $matchTujuan);
                preg_match('/Periode:\s*([^\s]+)\s+s\.d\s+([^).]+)/i', $app->catatan, $matchPeriode);

                $namaPeminjam = isset($matchNama[1]) ? trim($matchNama[1]) : 'Warga RT';
                $nikPeminjam = isset($matchNama[2]) ? trim($matchNama[2]) : null;
                $tujuan = isset($matchTujuan[1]) ? trim($matchTujuan[1]) : $app->catatan;
                $tglMulai = isset($matchPeriode[1]) ? trim($matchPeriode[1]) : '-';
                $tglSelesai = isset($matchPeriode[2]) ? trim($matchPeriode[2]) : '-';

                $warga = $nikPeminjam ? Warga::withoutGlobalScope('tenant')->find($nikPeminjam) : null;
                $kontak = $warga ? ($warga->no_telp ?? '-') : '-';
                $alamat = $warga && $warga->kartuKeluarga ? $warga->kartuKeluarga->Alamat : '-';

                $statusLower = strtolower($app->status ?? 'menunggu');
                $isDisetujui = in_array($statusLower, ['disetujui', 'approved']);
                $isPending = in_array($statusLower, ['menunggu', 'pending']);
                $isRejected = in_array($statusLower, ['ditolak', 'rejected']);

                $merged->push([
                    'id' => 'approval-' . $app->id_approval,
                    'loan_id' => null,
                    'approval_id' => $app->id_approval,
                    'nama' => $namaPeminjam,
                    'kontak' => $kontak,
                    'alamat' => $alamat,
                    'tgl_mulai' => $tglMulai,
                    'tgl_selesai' => $tglSelesai,
                    'tujuan' => $tujuan,
                    'status' => $statusLower,
                    'status_label' => $isDisetujui ? 'Sedang Dipinjam' : ($isPending ? 'Menunggu Konfirmasi' : ($isRejected ? 'Ditolak' : 'Sudah Dikembalikan')),
                    'status_color' => $isDisetujui ? 'warning' : ($isPending ? 'info' : ($isRejected ? 'danger' : 'success')),
                    'is_active' => $isDisetujui || $isPending,
                    'can_return' => $isDisetujui,
                ]);
            }
        }

        return $merged;
    }

    /**
     * Ringkasan peminjam aktif saat ini untuk ditampilkan di kolom tabel
     */
    public function getActiveBorrowersSummary(): ?string
    {
        $active = $this->getDaftarPeminjam()->where('is_active', true);
        if ($active->isEmpty()) {
            return null;
        }

        if ($active->count() === 1) {
            $first = $active->first();
            return "{$first['nama']} ({$first['status_label']})";
        }

        $names = $active->pluck('nama')->take(2)->implode(', ');
        $more = $active->count() > 2 ? ' (+' . ($active->count() - 2) . ' lainnya)' : '';
        return "{$names}{$more}";
    }
}
