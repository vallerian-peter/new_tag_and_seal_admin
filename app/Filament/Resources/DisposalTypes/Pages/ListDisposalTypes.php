<?php

namespace App\Filament\Resources\DisposalTypes\Pages;

use App\Filament\Resources\DisposalTypes\DisposalTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisposalTypes extends ListRecords
{
    protected static string $resource = DisposalTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

