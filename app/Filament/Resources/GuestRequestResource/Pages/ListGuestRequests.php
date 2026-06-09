<?php

declare(strict_types=1);

namespace App\Filament\Resources\GuestRequestResource\Pages;

use App\Filament\Resources\GuestRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListGuestRequests extends ListRecords
{
    protected static string $resource = GuestRequestResource::class;
}
