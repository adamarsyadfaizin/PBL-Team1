<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactFaqResource\Pages;

use App\Filament\Resources\ContactFaqResource;
use Filament\Resources\Pages\ListRecords;

class ListContactFaqs extends ListRecords
{
    protected static string $resource = ContactFaqResource::class;
}
