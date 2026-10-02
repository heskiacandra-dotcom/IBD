<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLoan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
        'tgl_kembali' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(AssetItem::class, 'asset_item_id');
    }

    public function ticket()
    {
        return $this->belongsTo(ApprovalTicket::class, 'ticket_code', 'ticket_code');
    }
}
