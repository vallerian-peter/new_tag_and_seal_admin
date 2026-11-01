<?php

namespace App\Filament\Resources\EventDefinitions\Pages;

use App\Filament\Resources\EventDefinitions\EventDefinitionResource;
use Filament\Resources\Pages\EditRecord;

class EditEventDefinition extends EditRecord
{
    protected static string $resource = EventDefinitionResource::class;
}
