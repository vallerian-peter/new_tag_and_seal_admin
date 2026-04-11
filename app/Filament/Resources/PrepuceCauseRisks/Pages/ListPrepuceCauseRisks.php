<?php

namespace App\Filament\Resources\PrepuceCauseRisks\Pages;

use App\Filament\Resources\PrepuceCauseRisks\PrepuceCauseRiskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrepuceCauseRisks extends ListRecords
{
    protected static string $resource = PrepuceCauseRiskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

