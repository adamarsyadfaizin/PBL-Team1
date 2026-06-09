<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactLocationResource\Pages;

use App\Filament\Resources\ContactLocationResource;
use Filament\Resources\Pages\ListRecords;

class ListContactLocations extends ListRecords
{
    protected static string $resource = ContactLocationResource::class;
}
