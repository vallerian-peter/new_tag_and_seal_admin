<?php

namespace App\Filament\Resources\IdentificationEvents\Pages;

use App\Filament\Resources\IdentificationEvents\IdentificationEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIdentificationEvents extends ListRecords
{
    protected static string $resource = IdentificationEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

