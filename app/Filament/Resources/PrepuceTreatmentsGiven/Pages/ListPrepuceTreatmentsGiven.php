<?php

namespace App\Filament\Resources\PrepuceTreatmentsGiven\Pages;

use App\Filament\Resources\PrepuceTreatmentsGiven\PrepuceTreatmentGivenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrepuceTreatmentsGiven extends ListRecords
{
    protected static string $resource = PrepuceTreatmentGivenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

