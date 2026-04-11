<?php

namespace App\Filament\Resources\IronInjections\Pages;

use App\Filament\Resources\IronInjections\IronInjectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIronInjections extends ListRecords
{
    protected static string $resource = IronInjectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

