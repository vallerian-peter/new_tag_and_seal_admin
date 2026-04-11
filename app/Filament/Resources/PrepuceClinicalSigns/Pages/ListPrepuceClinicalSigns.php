<?php

namespace App\Filament\Resources\PrepuceClinicalSigns\Pages;

use App\Filament\Resources\PrepuceClinicalSigns\PrepuceClinicalSignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrepuceClinicalSigns extends ListRecords
{
    protected static string $resource = PrepuceClinicalSignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

