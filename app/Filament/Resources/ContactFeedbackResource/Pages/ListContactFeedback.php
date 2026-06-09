<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactFeedbackResource\Pages;

use App\Filament\Resources\ContactFeedbackResource;
use Filament\Resources\Pages\ListRecords;

class ListContactFeedback extends ListRecords
{
    protected static string $resource = ContactFeedbackResource::class;
}
