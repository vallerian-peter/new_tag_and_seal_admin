<?php

namespace App\Filament\Resources\WeightChanges\Pages;

use App\Filament\Resources\WeightChanges\WeightChangeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWeightChanges extends ListRecords
{
    protected static string $resource = WeightChangeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}


