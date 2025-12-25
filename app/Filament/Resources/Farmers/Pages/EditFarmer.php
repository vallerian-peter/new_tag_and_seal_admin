<?php

namespace App\Filament\Resources\Farmers\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Farmers\FarmerResource;
use App\Models\Farm;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFarmer extends EditRecord
{
    protected static string $resource = FarmerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    // Delete all Farms (which cascades to Livestocks, Vaccines, and logs)
                    $this->deleteFarmerRelatedData();
                    
                    // Delete linked User account before deleting Farmer
                    $this->deleteLinkedUserAccount();
                }),
        ];
    }

    /**
     * Delete all Farms associated with this farmer.
     * Farm deletion will cascade to Livestocks, Vaccines, and all Livestock logs.
     */
    protected function deleteFarmerRelatedData(): void
    {
        $farmer = $this->record;
        $farmerId = $farmer->id;

        // Get all farms owned by this farmer
        $farms = Farm::where('farmerId', $farmerId)->get();

        // Delete each farm (which will cascade to Livestocks, Vaccines, and logs)
        foreach ($farms as $farm) {
            // Delete all Livestocks and Vaccines for this farm
            $this->deleteFarmRelatedData($farm);
            // Then delete the farm
            $farm->delete();
        }
    }

    /**
     * Delete all Livestocks and Vaccines associated with a farm.
     * Livestock deletion will cascade to all their logs.
     */
    protected function deleteFarmRelatedData($farm): void
    {
        $farmUuid = $farm->uuid;

        // Get all livestocks in this farm
        $livestocks = \App\Models\Livestock::where('farmUuid', $farmUuid)->get();
        
        // Delete each livestock (which will cascade to all their logs)
        foreach ($livestocks as $livestock) {
            // Delete all livestock logs first
            $this->deleteLivestockLogs($livestock);
            // Then delete the livestock
            $livestock->delete();
        }

        // Delete all Vaccines registered on this farm
        \App\Models\Vaccine::where('farmUuid', $farmUuid)->delete();
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
        \App\Models\Treatment::where('livestockUuid', $livestockUuid)->delete();
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

    /**
     * Delete linked User account before deleting Farmer.
     */
    protected function deleteLinkedUserAccount(): void
    {
        $farmer = $this->record;

        // Find User where roleId matches Farmer id and role is FARMER
        $linkedUser = User::where('roleId', $farmer->id)
            ->where('role', UserRole::FARMER)
            ->first();

        if ($linkedUser) {
            $userEmail = $linkedUser->email ?? 'N/A';
            $linkedUser->delete();

            Notification::make()
                ->title('User account deleted')
                ->body("Linked login account ({$userEmail}) has been deleted.")
                ->success()
                ->send();
        }
    }
}
