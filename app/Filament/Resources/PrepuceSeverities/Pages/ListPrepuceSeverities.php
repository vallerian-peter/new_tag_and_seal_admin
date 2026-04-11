<?php

namespace App\Filament\Resources\PrepuceSeverities\Pages;

use App\Filament\Resources\PrepuceSeverities\PrepuceSeverityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrepuceSeverities extends ListRecords
{
    protected static string $resource = PrepuceSeverityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

