<?php

declare(strict_types=1);

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoom extends EditRecord
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Kamar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Kamar berhasil diperbarui';
    }
}
