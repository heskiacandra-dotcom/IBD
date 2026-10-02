<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function loans()
    {
        return $this->hasMany(AssetLoan::class, 'asset_item_id');
    }

    public function activeLoans()
    {
        return $this->hasMany(AssetLoan::class, 'asset_item_id')->where('status', 'borrowed');
    }
}
