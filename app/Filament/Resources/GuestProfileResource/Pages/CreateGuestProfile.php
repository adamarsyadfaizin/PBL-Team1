<?php

declare(strict_types=1);

namespace App\Filament\Resources\GuestProfileResource\Pages;

use App\Filament\Resources\GuestProfileResource;
use App\Models\GuestProfile;
use Filament\Resources\Pages\CreateRecord;

class CreateGuestProfile extends CreateRecord
{
    protected static string $resource = GuestProfileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return array_merge(GuestProfile::defaults(), $data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Profil guest house berhasil dibuat';
    }
}
