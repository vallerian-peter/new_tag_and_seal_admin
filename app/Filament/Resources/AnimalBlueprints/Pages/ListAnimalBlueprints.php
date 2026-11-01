<?php

namespace App\Filament\Resources\AnimalBlueprints\Pages;

use App\Filament\Resources\AnimalBlueprints\AnimalBlueprintResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnimalBlueprints extends ListRecords
{
    protected static string $resource = AnimalBlueprintResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
