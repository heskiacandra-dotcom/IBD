<?php

namespace App\Filament\Resources\Admins\Pages;

use App\Filament\Resources\Admins\AdminResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdmins extends ListRecords
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(fn () => auth()->user()?->role === 'admin_rt' ? 'Daftarkan Pengurus RT' : 'Tambah Pengurus / Admin')
                ->icon('heroicon-m-user-plus')
                ->modalHeading(fn () => auth()->user()?->role === 'admin_rt' ? 'Daftarkan Pengurus RT (Sekretaris / Bendahara)' : 'Daftarkan Pengurus Baru')
                ->modalDescription('Buat akun pengurus baru untuk membantu pengelolaan administrasi dan keuangan RT.')
                ->slideOver(),
        ];
    }
}
