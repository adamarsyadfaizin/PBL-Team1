<?php

declare(strict_types=1);

namespace App\Filament\Resources\GuestProfileResource\Pages;

use App\Filament\Resources\GuestProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuestProfile extends EditRecord
{
    protected static string $resource = GuestProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Profil'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Profil guest house berhasil diperbarui';
    }
}
