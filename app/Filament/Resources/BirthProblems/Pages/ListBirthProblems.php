<?php

namespace App\Filament\Resources\BirthProblems\Pages;

use App\Filament\Resources\BirthProblems\BirthProblemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBirthProblems extends ListRecords
{
    protected static string $resource = BirthProblemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

