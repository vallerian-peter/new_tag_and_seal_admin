<?php

namespace App\Filament\Resources\LivestockMarkings\Pages;

use App\Filament\Resources\LivestockMarkings\LivestockMarkingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLivestockMarkings extends ListRecords
{
    protected static string $resource = LivestockMarkingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

