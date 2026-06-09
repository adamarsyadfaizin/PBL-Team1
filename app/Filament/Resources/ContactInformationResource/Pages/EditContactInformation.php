<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactInformationResource\Pages;

use App\Filament\Resources\ContactInformationResource;
use Filament\Resources\Pages\EditRecord;

class EditContactInformation extends EditRecord
{
    protected static string $resource = ContactInformationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Informasi kontak berhasil diperbarui';
    }
}
