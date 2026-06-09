<?php

declare(strict_types=1);

namespace App\Filament\Resources\GuestRequestResource\Pages;

use App\Filament\Resources\GuestRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuestRequest extends EditRecord
{
    protected static string $resource = GuestRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Status permintaan tamu berhasil diperbarui';
    }
}
