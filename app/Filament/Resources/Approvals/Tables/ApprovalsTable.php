<?php

namespace App\Filament\Resources\Approvals\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class ApprovalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id_approval')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => '#APP-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->sortable(),
                TextColumn::make('tabel_sumber')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match (strtoupper($state)) {
                        'SURAT' => 'info',
                        'PEMASUKAN' => 'success',
                        'INVENTARIS' => 'warning',
                        'KEGIATAN' => 'primary',
                        'PENGELUARAN' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('source_detail')
                    ->label('Uraian Pengajuan')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('catatan')
                    ->label('Catatan Verifikasi')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'disetujui' => 'success',
                        'menunggu', 'pending' => 'warning',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('tgl_approval')
                    ->label('Waktu Proses')
                    ->dateTime('d M Y, H:i \W\I\B')
                    ->placeholder('Menunggu tindakan')
                    ->sortable(),
            ])
            ->defaultSort('id_approval', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Menunggu' => 'Menunggu Tindakan',
                        'Disetujui' => 'Disetujui',
                        'Ditolak' => 'Ditolak',
                    ]),
                SelectFilter::make('tabel_sumber')
                    ->label('Filter Kategori')
                    ->options([
                        'SURAT' => 'Pelayanan Surat',
                        'PEMASUKAN' => 'Iuran / Keuangan',
                        'INVENTARIS' => 'Peminjaman Aset',
                        'KEGIATAN' => 'Agenda Lingkungan',
                        'PENGELUARAN' => 'Belanja Operasional',
                    ]),
            ])
            ->recordActions([
                Action::make('review_tiket')
                    ->label('Review Tiket')
                    ->icon('heroicon-o-document-magnifying-glass')
                    ->color('primary')
                    ->slideOver()
                    ->modalHeading(fn (Approval $record) => 'Detail & Verifikasi Berkas Tiket #APP-' . str_pad($record->id_approval, 4, '0', STR_PAD_LEFT))
                    ->modalWidth('2xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (Approval $record) => view('filament.modals.review-tiket-approval', ['record' => $record]))
                    ->extraModalFooterActions(function (Approval $record): array {
                        $isPending = in_array(strtolower($record->status), ['menunggu', 'pending']);
                        if (!$isPending) {
                            return [];
                        }

                        return [
                            Action::make('drawer_approve')
                                ->label('Setujui Pengajuan')
                                ->icon('heroicon-m-check-circle')
                                ->color('success')
                                ->requiresConfirmation()
                                ->modalHeading('Konfirmasi Persetujuan')
                                ->modalDescription(fn () => "Apakah Anda yakin ingin menyetujui pengajuan {$record->source_detail}?")
                                ->action(function () use ($record) {
                                    DB::transaction(function () use ($record) {
                                        $record->update([
                                            'status' => 'approved',
                                            'tgl_approval' => now(),
                                            'Admin_ID' => auth()->id() ?? $record->Admin_ID ?? 1,
                                        ]);

                                        if ($record->tabel_sumber === 'SURAT') {
                                            $surat = \App\Models\Surat::with('warga')->find($record->id_referensi);
                                            if ($surat) {
                                                $nomorSurat = $surat->nomor_surat ?: \App\Models\Surat::generateNomorSurat($surat->surat_id, $surat->warga?->ID_RT);
                                                $surat->update([
                                                    'status' => 'approved',
                                                    'nomor_surat' => $nomorSurat,
                                                ]);
                                            }
                                        }
                                        if ($record->tabel_sumber === 'PEMASUKAN') {
                                            \App\Models\Pemasukan::where('Tagihan_Id', $record->id_referensi)
                                                ->update(['status_tagihan' => 'lunas']);
                                        }
                                        if ($record->tabel_sumber === 'INVENTARIS') {
                                            $inv = \App\Models\Inventaris::find($record->id_referensi);
                                            if ($inv && $inv->Jumlah > 0) {
                                                $inv->decrement('Jumlah', 1);
                                            }
                                            \App\Models\AssetLoan::where(function ($q) use ($inv) {
                                                if ($inv) {
                                                    $q->where('kode_aset', $inv->Kode_Barang)->orWhere('nama_alat', $inv->Nama_Barang);
                                                }
                                            })->where('status', 'pending')->latest()->first()?->update([
                                                'status' => 'borrowed',
                                                'approved_by' => auth()->user()?->Username ?? 'Pengurus RT',
                                            ]);
                                        }
                                        if ($record->tabel_sumber === 'KEGIATAN') {
                                            \App\Models\Kegiatan::where('kegiatan_id', $record->id_referensi)
                                                ->update(['status' => 'Mendatang']);
                                        }
                                    });

                                    Notification::make()
                                        ->title('Pengajuan Disetujui')
                                        ->body("Pengajuan #APP-{$record->id_approval} ({$record->tabel_sumber}) telah disahkan.")
                                        ->success()
                                        ->send();
                                }),

                            Action::make('drawer_reject')
                                ->label('Tolak Pengajuan')
                                ->icon('heroicon-m-x-circle')
                                ->color('danger')
                                ->requiresConfirmation()
                                ->modalHeading('Tolak Pengajuan')
                                ->modalDescription('Apakah Anda yakin ingin menolak pengajuan ini?')
                                ->form([
                                    Textarea::make('catatan')
                                        ->label('Alasan Penolakan')
                                        ->placeholder('Tuliskan alasan penolakan...')
                                        ->required(),
                                ])
                                ->action(function (array $data) use ($record) {
                                    DB::transaction(function () use ($record, $data) {
                                        $record->update([
                                            'status' => 'rejected',
                                            'catatan' => $data['catatan'],
                                            'tgl_approval' => now(),
                                            'Admin_ID' => auth()->id() ?? $record->Admin_ID ?? 1,
                                        ]);

                                        if ($record->tabel_sumber === 'SURAT') {
                                            \App\Models\Surat::where('surat_id', $record->id_referensi)->update(['status' => 'rejected']);
                                        }
                                        if ($record->tabel_sumber === 'PEMASUKAN') {
                                            \App\Models\Pemasukan::where('Tagihan_Id', $record->id_referensi)->update(['status_tagihan' => 'belum_lunas']);
                                        }
                                        if ($record->tabel_sumber === 'INVENTARIS') {
                                            $inv = \App\Models\Inventaris::find($record->id_referensi);
                                            \App\Models\AssetLoan::where(function ($q) use ($inv) {
                                                if ($inv) {
                                                    $q->where('kode_aset', $inv->Kode_Barang)->orWhere('nama_alat', $inv->Nama_Barang);
                                                }
                                            })->where('status', 'pending')->latest()->first()?->update([
                                                'status' => 'rejected',
                                            ]);
                                        }
                                        if ($record->tabel_sumber === 'KEGIATAN') {
                                            \App\Models\Kegiatan::where('kegiatan_id', $record->id_referensi)->update(['status' => 'Dibatalkan']);
                                        }
                                    });

                                    Notification::make()
                                        ->title('Pengajuan Ditolak')
                                        ->body("Pengajuan #APP-{$record->id_approval} telah ditolak.")
                                        ->danger()
                                        ->send();
                                }),
                        ];
                    }),

                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => in_array(strtolower($record->status), ['menunggu', 'pending']))
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Persetujuan')
                    ->modalDescription(fn ($record) => "Apakah Anda yakin ingin menyetujui pengajuan {$record->source_detail}?")
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $record->update([
                                'status' => 'approved',
                                'tgl_approval' => now(),
                                'Admin_ID' => auth()->id() ?? $record->Admin_ID ?? 1,
                            ]);

                            if ($record->tabel_sumber === 'SURAT') {
                                $surat = \App\Models\Surat::with('warga')->find($record->id_referensi);
                                if ($surat) {
                                    $nomorSurat = $surat->nomor_surat ?: \App\Models\Surat::generateNomorSurat($surat->surat_id, $surat->warga?->ID_RT);
                                    $surat->update([
                                        'status' => 'approved',
                                        'nomor_surat' => $nomorSurat,
                                    ]);
                                }
                            }
                            if ($record->tabel_sumber === 'PEMASUKAN') {
                                \App\Models\Pemasukan::where('Tagihan_Id', $record->id_referensi)->update(['status_tagihan' => 'lunas']);
                            }
                            if ($record->tabel_sumber === 'INVENTARIS') {
                                $inv = \App\Models\Inventaris::find($record->id_referensi);
                                if ($inv && $inv->Jumlah > 0) {
                                    $inv->decrement('Jumlah', 1);
                                }
                                \App\Models\AssetLoan::where(function ($q) use ($inv) {
                                    if ($inv) {
                                        $q->where('kode_aset', $inv->Kode_Barang)->orWhere('nama_alat', $inv->Nama_Barang);
                                    }
                                })->where('status', 'pending')->latest()->first()?->update([
                                    'status' => 'borrowed',
                                    'approved_by' => auth()->user()?->Username ?? 'Pengurus RT',
                                ]);
                            }
                            if ($record->tabel_sumber === 'KEGIATAN') {
                                \App\Models\Kegiatan::where('kegiatan_id', $record->id_referensi)->update(['status' => 'Mendatang']);
                            }
                        });

                        Notification::make()
                            ->title('Pengajuan Berhasil Disetujui')
                            ->body("Pengajuan #APP-{$record->id_approval} ({$record->tabel_sumber}) telah disahkan.")
                            ->success()
                            ->send();
                    }),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array(strtolower($record->status), ['menunggu', 'pending']))
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengajuan')
                    ->modalDescription(fn ($record) => "Apakah Anda yakin ingin menolak pengajuan ini?")
                    ->form([
                        Textarea::make('catatan')
                            ->label('Alasan Penolakan')
                            ->placeholder('Tuliskan alasan penolakan...')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        DB::transaction(function () use ($record, $data) {
                            $record->update([
                                'status' => 'rejected',
                                'catatan' => $data['catatan'],
                                'tgl_approval' => now(),
                                'Admin_ID' => auth()->id() ?? $record->Admin_ID ?? 1,
                            ]);

                            if ($record->tabel_sumber === 'SURAT') {
                                \App\Models\Surat::where('surat_id', $record->id_referensi)->update(['status' => 'rejected']);
                            }
                            if ($record->tabel_sumber === 'PEMASUKAN') {
                                \App\Models\Pemasukan::where('Tagihan_Id', $record->id_referensi)->update(['status_tagihan' => 'belum_lunas']);
                            }
                            if ($record->tabel_sumber === 'INVENTARIS') {
                                $inv = \App\Models\Inventaris::find($record->id_referensi);
                                \App\Models\AssetLoan::where(function ($q) use ($inv) {
                                    if ($inv) {
                                        $q->where('kode_aset', $inv->Kode_Barang)->orWhere('nama_alat', $inv->Nama_Barang);
                                    }
                                })->where('status', 'pending')->latest()->first()?->update([
                                    'status' => 'rejected',
                                ]);
                            }
                            if ($record->tabel_sumber === 'KEGIATAN') {
                                \App\Models\Kegiatan::where('kegiatan_id', $record->id_referensi)->update(['status' => 'Dibatalkan']);
                            }
                        });

                        Notification::make()
                            ->title('Pengajuan Ditolak')
                            ->body("Pengajuan #APP-{$record->id_approval} telah ditolak.")
                            ->danger()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
