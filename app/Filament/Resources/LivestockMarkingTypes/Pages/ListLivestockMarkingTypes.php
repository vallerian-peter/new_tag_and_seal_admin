<?php

namespace App\Filament\Resources\LivestockMarkingTypes\Pages;

use App\Filament\Resources\LivestockMarkingTypes\LivestockMarkingTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLivestockMarkingTypes extends ListRecords
{
    protected static string $resource = LivestockMarkingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
