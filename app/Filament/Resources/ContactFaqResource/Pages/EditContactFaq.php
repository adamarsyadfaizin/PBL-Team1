<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactFaqResource\Pages;

use App\Filament\Resources\ContactFaqResource;
use Filament\Resources\Pages\EditRecord;

class EditContactFaq extends EditRecord
{
    protected static string $resource = ContactFaqResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'FAQ kontak berhasil diperbarui';
    }
}
