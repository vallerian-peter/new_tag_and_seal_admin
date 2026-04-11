<?php

namespace App\Filament\Resources\PrepuceConditions\Pages;

use App\Filament\Resources\PrepuceConditions\PrepuceConditionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrepuceConditions extends ListRecords
{
    protected static string $resource = PrepuceConditionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

