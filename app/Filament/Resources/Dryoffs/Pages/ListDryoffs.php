<?php

namespace App\Filament\Resources\Dryoffs\Pages;

use App\Filament\Resources\Dryoffs\DryoffResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDryoffs extends ListRecords
{
    protected static string $resource = DryoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

