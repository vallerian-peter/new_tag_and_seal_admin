<?php

namespace App\Filament\Resources\TeethClippingMethods\Pages;

use App\Filament\Resources\TeethClippingMethods\TeethClippingMethodResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeethClippingMethods extends ListRecords
{
    protected static string $resource = TeethClippingMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

