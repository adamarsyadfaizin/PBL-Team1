<?php

declare(strict_types=1);

namespace App\Filament\Resources\GuestProfileResource\Pages;

use App\Filament\Resources\GuestProfileResource;
use App\Models\GuestProfile;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuestProfiles extends ListRecords
{
    protected static string $resource = GuestProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Profil')
                ->visible(fn (): bool => ! GuestProfile::query()->exists()),
        ];
    }
}
