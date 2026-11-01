<?php

namespace App\Filament\Resources\AnimalSpecies\Pages;

use App\Filament\Resources\AnimalSpecies\AnimalSpeciesResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAnimalSpecies extends CreateRecord
{
    protected static string $resource = AnimalSpeciesResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_uuid'] = auth()->user()->uuid;

        return $data;
    }
}
