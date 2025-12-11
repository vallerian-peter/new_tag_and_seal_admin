<?php

namespace App\Filament\Resources\ReproductiveProblems\Pages;

use App\Filament\Resources\ReproductiveProblems\ReproductiveProblemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReproductiveProblems extends ListRecords
{
    protected static string $resource = ReproductiveProblemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

