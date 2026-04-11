<?php

namespace App\Filament\Resources\IdentificationEvents\Pages;

use App\Filament\Resources\IdentificationEvents\IdentificationEventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIdentificationEvent extends CreateRecord
{
    protected static string $resource = IdentificationEventResource::class;
}

