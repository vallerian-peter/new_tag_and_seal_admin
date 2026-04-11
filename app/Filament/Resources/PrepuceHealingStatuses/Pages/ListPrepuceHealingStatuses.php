<?php

namespace App\Filament\Resources\PrepuceHealingStatuses\Pages;

use App\Filament\Resources\PrepuceHealingStatuses\PrepuceHealingStatusResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrepuceHealingStatuses extends ListRecords
{
    protected static string $resource = PrepuceHealingStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

