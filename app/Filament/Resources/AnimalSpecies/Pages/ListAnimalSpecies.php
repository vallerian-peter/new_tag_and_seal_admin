<?php

namespace App\Filament\Resources\AnimalSpecies\Pages;

use App\Filament\Resources\AnimalSpecies\AnimalSpeciesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnimalSpecies extends ListRecords
{
    protected static string $resource = AnimalSpeciesResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
