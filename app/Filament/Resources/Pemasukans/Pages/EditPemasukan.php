<?php

namespace App\Filament\Resources\Pemasukans\Pages;

use App\Filament\Resources\Pemasukans\PemasukanResource;
use App\Services\MidtransService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPemasukan extends EditRecord
{
    protected static string $resource = PemasukanResource::class;

    protected ?string $previousStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $this->previousStatus = strtolower($this->record->getOriginal('status_tagihan') ?? '');
    }

    protected function afterSave(): void
    {
        $currentStatus = strtolower($this->record->status_tagihan ?? '');

        if ($this->previousStatus === 'lunas' && $currentStatus !== 'lunas') {
            $result = app(MidtransService::class)->cancelOrRefundTransaction(
                $this->record,
                'Diubah manual via Form Edit CMS'
            );

            Notification::make()
                ->title('Sinkronisasi Midtrans')
                ->body($result['message'])
                ->warning()
                ->send();
        }
    }
}
