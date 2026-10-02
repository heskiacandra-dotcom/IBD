<?php

namespace App\Filament\Resources\Admins\Schemas;

use App\Models\Admin;
use App\Models\DataRt;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Username')
                    ->label('Username Akun')
                    ->placeholder('Contoh: bendahara_rt05 atau siti_sekretaris')
                    ->required()
                    ->unique(Admin::class, 'Username', ignoreRecord: true)
                    ->maxLength(100)
                    ->regex('/^[a-zA-Z0-9_.]+$/')
                    ->validationMessages([
                        'unique' => 'Username ini sudah digunakan oleh akun lain.',
                        'regex' => 'Username hanya boleh berisi huruf, angka, titik, dan garis bawah (_).',
                    ])
                    ->helperText('Digunakan oleh pengurus untuk masuk ke panel RuangWarga.')
                    ->columnSpanFull(),

                Select::make('role')
                    ->label('Peran / Jabatan Pengurus')
                    ->options(function () {
                        $user = auth()->user();
                        if ($user?->role === 'admin_rt') {
                            return [
                                'bendahara' => 'Bendahara RT (Kelola Iuran, Pemasukan & Pengeluaran Kas)',
                                'sekretaris' => 'Sekretaris RT (Kelola Surat Pengantar & Data Warga)',
                            ];
                        }

                        return [
                            'super_admin' => 'Super Admin RW',
                            'admin_rt' => 'Ketua RT (Pimpinan Wilayah)',
                            'bendahara' => 'Bendahara RT (Kelola Iuran & Kas)',
                            'sekretaris' => 'Sekretaris RT (Kelola Surat & Warga)',
                        ];
                    })
                    ->required()
                    ->default(fn () => auth()->user()?->role === 'admin_rt' ? 'sekretaris' : 'admin_rt')
                    ->helperText(fn () => auth()->user()?->role === 'admin_rt'
                        ? 'Ketua RT berwenang mendaftarkan dan mengatur Bendahara serta Sekretaris untuk wilayah RT miliknya.'
                        : 'Pilih wewenang dan hak akses pengurus sistem.')
                    ->columnSpanFull(),

                // RT Assignment
                Hidden::make('ID_RT')
                    ->default(fn () => auth()->user()?->ID_RT ?? 1)
                    ->visible(fn () => auth()->user()?->role === 'admin_rt')
                    ->dehydrated(fn () => auth()->user()?->role === 'admin_rt'),

                Select::make('ID_RT')
                    ->label('Wilayah Domisili RT')
                    ->options(fn () => DataRt::all()->mapWithKeys(fn ($rt) => [$rt->ID_RT => 'RT ' . str_pad($rt->nomor_rt ?? $rt->Nomor_RT ?? $rt->ID_RT, 2, '0', STR_PAD_LEFT)]))
                    ->visible(fn () => auth()->user()?->role === 'super_admin')
                    ->required(fn (callable $get) => $get('role') !== 'super_admin')
                    ->placeholder('Pilih Wilayah RT...')
                    ->columnSpanFull(),

                TextInput::make('Password_hash')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->minLength(8)
                    ->placeholder(fn (string $operation): string => $operation === 'create' ? 'Minimal 8 karakter...' : 'Kosongkan jika tidak ingin mengubah kata sandi')
                    ->helperText(fn (string $operation): string => $operation === 'create'
                        ? 'Berikan kata sandi awal kepada pengurus yang didaftarkan. Pengurus dapat menggantinya nanti.'
                        : 'Kosongkan kolom ini jika kata sandi tidak ingin diubah.')
                    ->columnSpanFull(),
            ]);
    }
}
