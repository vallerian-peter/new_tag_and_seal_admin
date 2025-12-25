<?php

namespace App\Filament\Resources\Livestocks\Pages;

use App\Filament\Resources\Livestocks\LivestockResource;
use App\Models\AbortedPregnancy;
use App\Models\BirthEvent;
use App\Models\Calving;
use App\Models\Deworming;
use App\Models\Disposal;
use App\Models\Dryoff;
use App\Models\Feeding;
use App\Models\Insemination;
use App\Models\Treatment;
use App\Models\Milking;
use App\Models\Pregnancy;
use App\Models\Transfer;
use App\Models\Vaccination;
use App\Models\WeightChange;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLivestock extends EditRecord
{
    protected static string $resource = LivestockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    // Delete all livestock logs/events before deleting Livestock
                    $this->deleteLivestockLogs();
                }),
        ];
    }

    /**
     * Delete all logs/events associated with this livestock.
     */
    protected function deleteLivestockLogs(): void
    {
        $livestock = $this->record;
        $livestockUuid = $livestock->uuid;

        // Delete all 14+ log types
        BirthEvent::where('livestockUuid', $livestockUuid)->delete();
        AbortedPregnancy::where('livestockUuid', $livestockUuid)->delete();
        WeightChange::where('livestockUuid', $livestockUuid)->delete();
        Vaccination::where('livestockUuid', $livestockUuid)->delete();
        Treatment::where('livestockUuid', $livestockUuid)->delete();
        Deworming::where('livestockUuid', $livestockUuid)->delete();
        Feeding::where('livestockUuid', $livestockUuid)->delete();
        Milking::where('livestockUuid', $livestockUuid)->delete();
        Insemination::where('livestockUuid', $livestockUuid)->delete();
        Pregnancy::where('livestockUuid', $livestockUuid)->delete();
        Dryoff::where('livestockUuid', $livestockUuid)->delete();
        Disposal::where('livestockUuid', $livestockUuid)->delete();
        Transfer::where('livestockUuid', $livestockUuid)->delete();
        Calving::where('livestockUuid', $livestockUuid)->delete();
    }
}
