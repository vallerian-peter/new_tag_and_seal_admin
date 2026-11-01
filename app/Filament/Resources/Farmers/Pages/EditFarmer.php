<?php

namespace App\Filament\Resources\Farmers\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Farmers\FarmerResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFarmer extends EditRecord
{
    protected static string $resource = FarmerResource::class;

    /**
     * After saving the farmer, update linked User login password if provided.
     */
    protected function afterSave(): void
    {
        $newPassword = $this->data['password'] ?? null;
        if (filled($newPassword)) {
            $farmer = $this->record;
            $linkedUser = User::where('roleId', $farmer->id)
                ->where('role', UserRole::FARMER)
                ->first();

            if ($linkedUser) {
                $linkedUser->password = \Illuminate\Support\Facades\Hash::make($newPassword);
                $linkedUser->save();

                Notification::make()
                    ->title('Password updated')
                    ->body("Login password for farmer {$farmer->firstName} was updated successfully.")
                    ->success()
                    ->send();
            }
        }
    }
}
