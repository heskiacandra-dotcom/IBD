<?php

namespace App\Filament\Resources\Wargas\Schemas;

use App\Models\DataRt;
use App\Models\KartuKeluarga;
use App\Models\Warga;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('NIK')
                    ->label('NIK (Nomor Induk Kependudukan)')
                    ->required()
                    ->length(16)
                    ->regex('/^[0-9]{16}$/')
                    ->validationMessages([
                        'regex' => 'NIK harus berupa 16 digit angka.',
                        'unique' => 'NIK ini sudah terdaftar dalam sistem.',
                    ])
                    ->unique(Warga::class, 'NIK', ignoreRecord: true)
                    ->placeholder('16 Digit NIK KTP Warga...')
                    ->helperText('Nomor Induk Kependudukan 16 digit sesuai e-KTP.')
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->dehydrated(),

                TextInput::make('Nama_Lengkap')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(150)
                    ->placeholder('Nama lengkap sesuai KTP...'),

                Select::make('ID_KK')
                    ->label('Nomor Kartu Keluarga (No. KK)')
                    ->options(fn () => KartuKeluarga::all()->mapWithKeys(fn ($kk) => [
                        $kk->ID_KK => $kk->ID_KK . ($kk->Alamat ? ' - ' . Str::limit($kk->Alamat, 35) : '')
                    ]))
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('ID_KK')
                            ->label('Nomor KK (16 Digit)')
                            ->required()
                            ->length(16)
                            ->regex('/^[0-9]{16}$/')
                            ->validationMessages([
                                'regex' => 'Nomor KK harus berupa 16 digit angka.',
                                'unique' => 'Nomor KK ini sudah terdaftar.',
                            ])
                            ->unique(KartuKeluarga::class, 'ID_KK')
                            ->placeholder('Nomor KK 16 digit...'),
                        TextInput::make('Alamat')
                            ->label('Alamat Domisili Keluarga')
                            ->required()
                            ->placeholder('Contoh: Blok A No. 12 Harmoni Residence'),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        $kk = KartuKeluarga::create([
                            'ID_KK' => (string) $data['ID_KK'],
                            'Alamat' => $data['Alamat'],
                        ]);
                        return (string) $kk->ID_KK;
                    })
                    ->placeholder('Pilih atau Tambah No. KK...')
                    ->helperText('Pilih dari daftar KK yang ada, atau klik tanda (+) untuk menambahkan nomor KK baru.'),

                Select::make('ID_RT')
                    ->label('Wilayah RT')
                    ->options(fn () => DataRt::all()->mapWithKeys(fn ($rt) => [
                        $rt->ID_RT => 'RT ' . str_pad($rt->nomor_rt ?? $rt->Nomor_RT ?? $rt->ID_RT, 2, '0', STR_PAD_LEFT) . ' / RW ' . str_pad($rt->nomor_rw ?? $rt->Nomor_RW ?? 1, 2, '0', STR_PAD_LEFT)
                    ]))
                    ->default(fn () => auth()->user()?->ID_RT ?? 1)
                    ->required()
                    ->searchable(),

                TextInput::make('no_telp')
                    ->label('Nomor WhatsApp / HP')
                    ->tel()
                    ->maxLength(25)
                    ->placeholder('08xxxxxxxxxx')
                    ->helperText('Digunakan untuk notifikasi status permohonan surat & iuran kas.'),

                DatePicker::make('tgl_lahir')
                    ->label('Tanggal Lahir')
                    ->maxDate(now())
                    ->displayFormat('d/m/Y'),

                TextInput::make('Username')
                    ->label('Username Login Portal')
                    ->unique(Warga::class, 'Username', ignoreRecord: true)
                    ->placeholder('Username untuk login portal (opsional)...')
                    ->helperText('Jika dikosongkan, username otomatis diatur sama dengan NIK.'),

                TextInput::make('Password_hash')
                    ->label('Kata Sandi Akun')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                    ->placeholder(fn (string $operation): string => $operation === 'create' ? 'Kata sandi akun warga (default: 12345678)...' : 'Kosongkan jika tidak ingin mengubah kata sandi')
                    ->helperText('Kata sandi untuk login warga di Portal Mandiri. Default: 12345678 jika dikosongkan.'),

                Select::make('role')
                    ->label('Peran Warga')
                    ->options([
                        'warga' => 'Warga',
                        'kepala_keluarga' => 'Kepala Keluarga',
                    ])
                    ->default('warga')
                    ->required(),
            ]);
    }
}
