<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactInformationResource\Pages;

use App\Filament\Resources\ContactInformationResource;
use Filament\Resources\Pages\ListRecords;

class ListContactInformation extends ListRecords
{
    protected static string $resource = ContactInformationResource::class;
}
