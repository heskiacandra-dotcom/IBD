<?php

namespace App\Filament\Resources\DataRts\Tables;

use App\Models\Admin;
use App\Models\DataRt;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DataRtsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ID_RT')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('nomor_rt')
                    ->label('Unit RT')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($record) => 'RT ' . $record->nomor_rt . ' / RW ' . $record->nomor_rw)
                    ->searchable(),
                TextColumn::make('nama_ketua_rt')
                    ->label('Ketua RT')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('akun_login_rt')
                    ->label('Akun Login RT')
                    ->badge()
                    ->state(function (DataRt $record) {
                        $admin = $record->ketuaRtAdmin;
                        if (!$admin) {
                            return 'Belum Ada Akun';
                        }
                        if (filled($admin->google_id)) {
                            return $admin->Username . ' (Google Aktif)';
                        }
                        if (filled($admin->email)) {
                            return $admin->Username . ' (Google Siap)';
                        }
                        return $admin->Username . ' (Sandi)';
                    })
                    ->color(function (string $state) {
                        if ($state === 'Belum Ada Akun') return 'warning';
                        if (str_contains($state, 'Google Aktif')) return 'success';
                        if (str_contains($state, 'Google Siap')) return 'info';
                        return 'primary';
                    })
                    ->icon(function (string $state) {
                        if ($state === 'Belum Ada Akun') return 'heroicon-m-exclamation-triangle';
                        if (str_contains($state, 'Google Aktif')) return 'heroicon-m-check-badge';
                        if (str_contains($state, 'Google Siap')) return 'heroicon-m-arrow-right-end-on-rectangle';
                        return 'heroicon-m-user-circle';
                    }),
                TextColumn::make('kelurahan')
                    ->label('Kelurahan / Kecamatan')
                    ->formatStateUsing(fn ($record) => $record->kelurahan . ', ' . $record->kecamatan)
                    ->searchable(),
                TextColumn::make('kota')
                    ->label('Kota / Kabupaten')
                    ->searchable(),
                TextColumn::make('warga_count')
                    ->label('Populasi Warga')
                    ->counts('warga')
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => $state . ' Jiwa')
                    ->sortable(),
            ])
            ->defaultSort('ID_RT', 'asc')
            ->recordActions([
                Action::make('kelola_akun')
                    ->label('Akun RT')
                    ->tooltip('Kelola atau Buat Akun Login Ketua RT (Username, Sandi & Google SSO)')
                    ->icon('heroicon-m-key')
                    ->color('info')
                    ->modalHeading(fn (DataRt $record) => 'Akun Login Ketua RT - RT ' . str_pad($record->nomor_rt, 2, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($record->nomor_rw, 2, '0', STR_PAD_LEFT))
                    ->modalDescription('Super Admin dapat membuat atau mengatur akun login Ketua RT (bisa menggunakan Username & Password atau Email Google).')
                    ->fillForm(function (DataRt $record): array {
                        $admin = $record->ketuaRtAdmin;
                        return [
                            'username' => $admin?->Username ?? ('ketua_rt' . str_pad($record->nomor_rt, 2, '0', STR_PAD_LEFT)),
                            'email' => $admin?->email ?? '',
                        ];
                    })
                    ->schema([
                        TextInput::make('username')
                            ->label('Username Akun')
                            ->placeholder('Contoh: ketua_rt01')
                            ->required()
                            ->maxLength(100)
                            ->helperText('Username untuk masuk secara manual.'),

                        TextInput::make('email')
                            ->label('Email Google (Gmail untuk Google SSO)')
                            ->email()
                            ->placeholder('Contoh: ketua.rt01@gmail.com')
                            ->helperText('Jika diisi, Ketua RT dapat login dengan sekali klik menggunakan tombol "Masuk dengan Google".'),

                        TextInput::make('password')
                            ->label('Kata Sandi Akun')
                            ->password()
                            ->revealable()
                            ->minLength(8)
                            ->placeholder('Masukkan kata sandi baru atau kosongkan jika hanya menggunakan Google SSO...')
                            ->helperText('Jika email Google sudah diisi dan kata sandi dikosongkan, akun tetap dapat login menggunakan Google SSO.'),
                    ])
                    ->action(function (DataRt $record, array $data) {
                        $admin = $record->ketuaRtAdmin;
                        if ($admin) {
                            $admin->Username = trim($data['username']);
                            $admin->email = filled($data['email']) ? trim($data['email']) : null;
                            if (filled($data['password'])) {
                                $admin->Password_hash = Hash::make($data['password']);
                            }
                            $admin->save();

                            Notification::make()
                                ->success()
                                ->title('Akun Berhasil Diperbarui')
                                ->body("Akun Ketua RT {$record->nomor_rt} ({$admin->Username}) berhasil diperbarui.")
                                ->send();
                        } else {
                            $passwordHash = filled($data['password'])
                                ? Hash::make($data['password'])
                                : Hash::make(Str::random(32));

                            $newAdmin = Admin::create([
                                'Username' => trim($data['username']),
                                'email' => filled($data['email']) ? trim($data['email']) : null,
                                'Password_hash' => $passwordHash,
                                'role' => 'admin_rt',
                                'ID_RT' => $record->ID_RT,
                            ]);

                            Notification::make()
                                ->success()
                                ->title('Akun Berhasil Dibuat')
                                ->body("Akun Ketua RT {$record->nomor_rt} ({$newAdmin->Username}) berhasil dibuat dan siap digunakan.")
                                ->send();
                        }
                    }),
                ViewAction::make()->slideOver(),
                EditAction::make()->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => (auth('web')->user()?->role ?? auth()->user()?->role) === 'super_admin'),
                ]),
            ]);
    }
}
