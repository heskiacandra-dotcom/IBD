<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontakRt extends Model
{
    use HasFactory;

    protected $table = 'KONTAK_RT';
    protected $guarded = [];

    protected $casts = [
        'is_whatsapp' => 'boolean',
        'is_aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    public function dataRt()
    {
        return $this->belongsTo(DataRt::class, 'ID_RT', 'ID_RT');
    }

    /**
     * Get clean internationalized phone number digits starting with 62.
     */
    public function getCleanWhatsappNumberAttribute(): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $this->nomor_telepon);

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return $digits;
    }

    /**
     * Generate direct WhatsApp click-to-chat URL.
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        $digits = $this->clean_whatsapp_number;

        if (strlen($digits) < 8) {
            return null;
        }

        $pesan = $this->pesan_wa ?: "Halo {$this->role} ({$this->nama_kontak}), saya warga RT 05 ingin bertanya/melapor.";

        return "https://wa.me/{$digits}?text=" . rawurlencode($pesan);
    }

    /**
     * Generate standard tel: link for dialing.
     */
    public function getTelUrlAttribute(): string
    {
        $clean = preg_replace('/[^0-9+]/', '', (string) $this->nomor_telepon);
        return "tel:{$clean}";
    }

    /**
     * Formatted display label with WhatsApp indicator.
     */
    public function getDisplayLabelAttribute(): string
    {
        return $this->is_whatsapp 
            ? "{$this->nomor_telepon} (WhatsApp)" 
            : "{$this->nomor_telepon} (Panggilan)";
    }
}
