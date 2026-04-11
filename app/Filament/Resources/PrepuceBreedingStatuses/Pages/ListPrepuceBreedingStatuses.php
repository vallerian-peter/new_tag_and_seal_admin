<?php

namespace App\Filament\Resources\PrepuceBreedingStatuses\Pages;

use App\Filament\Resources\PrepuceBreedingStatuses\PrepuceBreedingStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrepuceBreedingStatuses extends ListRecords
{
    protected static string $resource = PrepuceBreedingStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

