<?php

namespace App\Filament\Resources\EventDefinitions\Pages;

use App\Filament\Resources\EventDefinitions\EventDefinitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventDefinition extends CreateRecord
{
    protected static string $resource = EventDefinitionResource::class;
}
