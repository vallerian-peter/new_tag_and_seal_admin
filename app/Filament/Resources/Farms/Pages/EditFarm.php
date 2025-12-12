<?php

namespace App\Filament\Resources\Farms\Pages;

use App\Filament\Resources\Farms\FarmResource;
use App\Models\Livestock;
use App\Models\Vaccine;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFarm extends EditRecord
{
    protected static string $resource = FarmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    // Delete all Livestocks and Vaccines before deleting Farm
                    $this->deleteFarmRelatedData();
                }),
        ];
    }

    /**
     * Delete all Livestocks and Vaccines associated with this farm.
     * Livestock deletion will cascade to all their logs.
     */
    protected function deleteFarmRelatedData(): void
    {
        $farm = $this->record;
        $farmUuid = $farm->uuid;

        // Get all livestocks in this farm
        $livestocks = Livestock::where('farmUuid', $farmUuid)->get();
        
        // Delete each livestock (which will cascade to all their logs)
        foreach ($livestocks as $livestock) {
            // Delete all livestock logs first
            $this->deleteLivestockLogs($livestock);
            // Then delete the livestock
            $livestock->delete();
        }

        // Delete all Vaccines registered on this farm
        Vaccine::where('farmUuid', $farmUuid)->delete();
    }

    /**
     * Delete all logs/events associated with a livestock.
     */
    protected function deleteLivestockLogs($livestock): void
    {
        $livestockUuid = $livestock->uuid;

        // Delete all 14+ log types
        \App\Models\BirthEvent::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\AbortedPregnancy::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\WeightChange::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Vaccination::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Medication::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Deworming::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Feeding::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Milking::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Insemination::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Pregnancy::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Dryoff::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Disposal::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Transfer::where('livestockUuid', $livestockUuid)->delete();
        \App\Models\Calving::where('livestockUuid', $livestockUuid)->delete();
    }
}
