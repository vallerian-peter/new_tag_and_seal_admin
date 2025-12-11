<?php

namespace App\Filament\Resources\CalvingProblems\Pages;

use App\Filament\Resources\CalvingProblems\CalvingProblemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalvingProblems extends ListRecords
{
    protected static string $resource = CalvingProblemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

