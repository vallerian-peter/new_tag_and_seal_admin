<?php

namespace App\Filament\Resources\ExtensionOfficerFarmInvites\Pages;

use App\Filament\Resources\ExtensionOfficerFarmInvites\ExtensionOfficerFarmInviteResource;
use App\Models\Farm;
use Filament\Resources\Pages\CreateRecord;

class CreateExtensionOfficerFarmInvite extends CreateRecord
{
    protected static string $resource = ExtensionOfficerFarmInviteResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If farmId is set, get the farmerId from the farm
        if (isset($data['farmId']) && !empty($data['farmId'])) {
            $farm = Farm::find($data['farmId']);
            if ($farm && $farm->farmerId) {
                $data['farmerId'] = $farm->farmerId;
            }
            // Remove farmId as it's not a fillable field
            unset($data['farmId']);
        }

        // Ensure farmerId is set (validation)
        if (empty($data['farmerId'])) {
            throw new \Exception('Please select a farm. The farmer ID is required.');
        }

        // Ensure access_code is generated if not set (use unique generation)
        if (empty($data['access_code'])) {
            $data['access_code'] = \App\Models\ExtensionOfficerFarmInvite::generateUniqueAccessCode();
        }

        // Ensure extensionOfficerId is set
        if (empty($data['extensionOfficerId'])) {
            throw new \Exception('Please select an extension officer.');
        }

        return $data;
    }
}
