<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactPlatformResource\Pages;

use App\Filament\Resources\ContactPlatformResource;
use Filament\Resources\Pages\ListRecords;

class ListContactPlatforms extends ListRecords
{
    protected static string $resource = ContactPlatformResource::class;
}
