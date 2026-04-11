<?php

namespace App\Filament\Resources\IdentificationEvents\Pages;

use App\Filament\Resources\IdentificationEvents\IdentificationEventResource;
use Filament\Resources\Pages\EditRecord;

class EditIdentificationEvent extends EditRecord
{
    protected static string $resource = IdentificationEventResource::class;
}

