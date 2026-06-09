<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactFeedbackResource\Pages;

use App\Filament\Resources\ContactFeedbackResource;
use Filament\Resources\Pages\EditRecord;

class EditContactFeedback extends EditRecord
{
    protected static string $resource = ContactFeedbackResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Umpan balik kontak berhasil diperbarui';
    }
}
