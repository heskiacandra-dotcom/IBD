<?php

namespace App\Filament\Resources\Admins\Schemas;

use App\Models\Admin;
use App\Models\DataRt;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('Username')
                    ->label('Username Akun')
                    ->placeholder('Contoh: ketua_rt01 atau bendahara_rt05')
                    ->required()
                    ->unique(Admin::class, 'Username', ignoreRecord: true)
                    ->maxLength(100)
                    ->regex('/^[a-zA-Z0-9_.]+$/')
                    ->validationMessages([
                        'unique' => 'Username ini sudah digunakan oleh akun lain.',
                        'regex' => 'Username hanya boleh berisi huruf, angka, titik, dan garis bawah (_).',
                    ])
                    ->helperText('Digunakan untuk masuk ke panel RuangWarga secara manual.')
                    ->columnSpanFull(),

                TextInput::make('email')
                    ->label('Email Google / Kontak (Google SSO)')
                    ->email()
                    ->placeholder('Contoh: ketua.rt01@gmail.com')
                    ->unique(Admin::class, 'email', ignoreRecord: true)
                    ->maxLength(150)
                    ->helperText('Jika diisi dengan akun Google (Gmail), pengurus dapat langsung masuk dengan satu klik melalui tombol "Masuk dengan Google".')
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
                    ->options(fn () => DataRt::all()->mapWithKeys(fn ($rt) => [
                        $rt->ID_RT => 'RT ' . str_pad($rt->nomor_rt, 2, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($rt->nomor_rw, 2, '0', STR_PAD_LEFT) . ' - ' . ($rt->kelurahan ?? 'Wilayah') . ' (' . ($rt->nama_ketua_rt ?? 'Ketua RT') . ')'
                    ]))
                    ->searchable()
                    ->visible(fn () => auth()->user()?->role === 'super_admin')
                    ->required(fn (callable $get) => $get('role') !== 'super_admin')
                    ->placeholder('Pilih Wilayah RT yang dikelola...')
                    ->helperText('Super Admin dapat memilih RT mana saja untuk akun pengurus ini.')
                    ->columnSpanFull(),

                TextInput::make('Password_hash')
                    ->label('Kata Sandi Akun')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation, callable $get): bool => $operation === 'create' && blank($get('email')))
                    ->dehydrated(fn (?string $state, callable $get, string $operation): bool => filled($state) || ($operation === 'create' && filled($get('email'))))
                    ->dehydrateStateUsing(fn (?string $state, callable $get): ?string => filled($state) ? Hash::make($state) : (filled($get('email')) ? Hash::make(Str::random(32)) : null))
                    ->minLength(8)
                    ->placeholder(fn (string $operation): string => $operation === 'create' ? 'Minimal 8 karakter (opsional jika email Google diisi)...' : 'Kosongkan jika tidak ingin mengubah kata sandi')
                    ->helperText(fn (string $operation): string => $operation === 'create'
                        ? 'Wajib diisi jika Email Google dikosongkan. Jika menggunakan Google SSO, kata sandi opsional dan akun dapat login langsung via Google.'
                        : 'Kosongkan kolom ini jika kata sandi tidak ingin diubah.')
                    ->columnSpanFull(),
            ]);
    }
}
