<?php

namespace App\Filament\Resources\LookupSets\Pages;

use App\Filament\Resources\LookupSets\LookupSetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLookupSets extends ListRecords
{
    protected static string $resource = LookupSetResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
