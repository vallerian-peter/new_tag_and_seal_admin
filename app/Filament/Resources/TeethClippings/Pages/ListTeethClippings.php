<?php

namespace App\Filament\Resources\TeethClippings\Pages;

use App\Filament\Resources\TeethClippings\TeethClippingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeethClippings extends ListRecords
{
    protected static string $resource = TeethClippingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

