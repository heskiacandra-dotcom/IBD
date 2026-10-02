<?php

namespace App\Filament\Resources\Aspirasis\Tables;

use App\Models\Aspirasi;
use App\Models\ResponAspirasi;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AspirasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_aspirasi')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#ASP-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y, H:i \W\I\B')
                    ->sortable(),

                TextColumn::make('warga.Nama_Lengkap')
                    ->label('Pengirim Aspirasi')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn ($record) => 'NIK: ' . $record->NIK),

                TextColumn::make('Isi_Aspirasi')
                    ->label('Cuplikan Isi Aspirasi')
                    ->searchable()
                    ->limit(65)
                    ->wrap(),

                TextColumn::make('responAspirasi.isi_respon')
                    ->label('Status Respon')
                    ->badge()
                    ->color(fn ($state) => filled($state) ? 'success' : 'warning')
                    ->formatStateUsing(fn ($state) => filled($state) ? 'Sudah Dijawab' : 'Menunggu Tanggapan'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('balas_aspirasi')
                    ->label(fn (Aspirasi $record) => $record->responAspirasi ? 'Edit Jawaban' : 'Tanggapi')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color(fn (Aspirasi $record) => $record->responAspirasi ? 'info' : 'primary')
                    ->slideOver()
                    ->modalHeading('Tanggapi Aspirasi Warga')
                    ->modalDescription('Kirimkan jawaban dan tindak lanjut resmi dari pengurus RT kepada warga pemohon.')
                    ->modalWidth('xl')
                    ->fillForm(fn (Aspirasi $record): array => [
                        'nama_responden' => $record->responAspirasi?->nama_responden ?? (auth()->user()?->Username ? 'Pengurus RT (' . auth()->user()->Username . ')' : 'Ketua RT 05'),
                        'isi_respon' => $record->responAspirasi?->isi_respon ?? '',
                    ])
                    ->form([
                        Placeholder::make('detail_warga')
                            ->label('Identitas Pengirim')
                            ->content(fn (Aspirasi $record) => ($record->warga?->Nama_Lengkap ?? 'Warga') . ' (NIK: ' . $record->NIK . ') • ' . ($record->created_at ? $record->created_at->translatedFormat('d F Y, H:i') : '-') . ' WIB'),

                        Placeholder::make('pesan_aspirasi')
                            ->label('Pesan Aspirasi Lengkap')
                            ->content(fn (Aspirasi $record) => $record->Isi_Aspirasi),

                        TextInput::make('nama_responden')
                            ->label('Nama Penjawab / Jabatan')
                            ->required()
                            ->maxLength(100),

                        Textarea::make('isi_respon')
                            ->label('Tanggapan Resmi Pengurus RT')
                            ->placeholder('Tuliskan respon resmi, klarifikasi, atau tindak lanjut pengurus RT terhadap aspirasi warga...')
                            ->required()
                            ->rows(6),
                    ])
                    ->action(function (Aspirasi $record, array $data) {
                        ResponAspirasi::updateOrCreate(
                            ['id_aspirasi' => $record->id_aspirasi],
                            [
                                'nama_responden' => $data['nama_responden'],
                                'isi_respon' => $data['isi_respon'],
                                'tanggal_respon' => now(),
                            ]
                        );

                        Notification::make()
                            ->title('Tanggapan Terkirim')
                            ->body('Tanggapan resmi pengurus berhasil disimpan dan tampil di portal warga.')
                            ->success()
                            ->send();
                    }),

                ViewAction::make()->slideOver(),
                EditAction::make()->slideOver(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
