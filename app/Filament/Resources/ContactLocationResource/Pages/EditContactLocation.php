<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactLocationResource\Pages;

use App\Filament\Resources\ContactLocationResource;
use Filament\Resources\Pages\EditRecord;

class EditContactLocation extends EditRecord
{
    protected static string $resource = ContactLocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Lokasi kontak berhasil diperbarui';
    }
}
