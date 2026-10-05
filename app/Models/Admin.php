<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;

class Admin extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory;

    public function canAccessPanel(Panel $panel): bool
    {
        $role = str_replace('-', '_', strtolower(trim($this->role ?? '')));
        return in_array($role, ['super_admin', 'admin_rt', 'bendahara', 'sekretaris', 'admin']);
    }

    public function getFilamentName(): string
    {
        return ($this->Username ?? 'Admin') . ' (' . ucfirst(str_replace('_', ' ', $this->role ?? 'admin')) . ')';
    }

    protected $table = 'ADMIN';
    protected $primaryKey = 'Admin_ID';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $guarded = [];

    protected $hidden = [
        'Password_hash',
        'remember_token',
    ];

    public function getAuthIdentifierName()
    {
        return 'Admin_ID';
    }

    public function getAuthIdentifier()
    {
        return $this->Admin_ID;
    }

    public function getAuthPassword()
    {
        return $this->Password_hash;
    }

    public function getAuthPasswordName()
    {
        return 'Password_hash';
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public function dataRt()
    {
        return $this->belongsTo(DataRt::class, 'ID_RT', 'ID_RT');
    }

    public function rt()
    {
        return $this->dataRt();
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'Admin_ID', 'Admin_ID');
    }
}
