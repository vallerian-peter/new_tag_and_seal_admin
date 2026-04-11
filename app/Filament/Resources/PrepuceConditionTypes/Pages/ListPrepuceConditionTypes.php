<?php

namespace App\Filament\Resources\PrepuceConditionTypes\Pages;

use App\Filament\Resources\PrepuceConditionTypes\PrepuceConditionTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrepuceConditionTypes extends ListRecords
{
    protected static string $resource = PrepuceConditionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

