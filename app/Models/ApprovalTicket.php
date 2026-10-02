<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'rt_unit_id',
        'citizen_id',
        'applicant',
        'house',
        'nik',
        'phone',
        'tabel_sumber',
        'tab',
        'perihal',
        'nominal',
        'status',
        'detail_lengkap',
        'preview_type',
        'preview_title',
        'preview_meta',
        'catatan_pengurus',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function rtUnit()
    {
        return $this->belongsTo(RtUnit::class);
    }

    public function citizen()
    {
        return $this->belongsTo(Citizen::class);
    }
}
