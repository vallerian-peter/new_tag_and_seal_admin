<?php

namespace App\Filament\Resources\AdministrationRoutes\Pages;

use App\Filament\Resources\AdministrationRoutes\AdministrationRouteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdministrationRoutes extends ListRecords
{
    protected static string $resource = AdministrationRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}


