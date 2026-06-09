<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPlatformResource\Pages;

use App\Filament\Resources\ContactPlatformResource;
use Filament\Resources\Pages\EditRecord;

class EditContactPlatform extends EditRecord
{
    protected static string $resource = ContactPlatformResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Platform digital berhasil diperbarui';
    }
}
