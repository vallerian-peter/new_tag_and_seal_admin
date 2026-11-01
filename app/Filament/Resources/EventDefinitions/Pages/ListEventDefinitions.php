<?php

namespace App\Filament\Resources\EventDefinitions\Pages;

use App\Filament\Resources\EventDefinitions\EventDefinitionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEventDefinitions extends ListRecords
{
    protected static string $resource = EventDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
