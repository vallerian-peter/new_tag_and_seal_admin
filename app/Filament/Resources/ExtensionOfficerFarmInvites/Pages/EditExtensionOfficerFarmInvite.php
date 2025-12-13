<?php

namespace App\Filament\Resources\ExtensionOfficerFarmInvites\Pages;

use App\Filament\Resources\ExtensionOfficerFarmInvites\ExtensionOfficerFarmInviteResource;
use App\Models\Farm;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExtensionOfficerFarmInvite extends EditRecord
{
    protected static string $resource = ExtensionOfficerFarmInviteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // If farmerId is set, find the first farm for this farmer and set farmId
        if (isset($data['farmerId']) && !empty($data['farmerId'])) {
            $farm = Farm::where('farmerId', $data['farmerId'])->first();
            if ($farm) {
                $data['farmId'] = $farm->id;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

        return $data;
    }
}
