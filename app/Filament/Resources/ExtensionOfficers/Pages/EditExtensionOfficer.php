<?php

namespace App\Filament\Resources\ExtensionOfficers\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\ExtensionOfficers\ExtensionOfficerResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditExtensionOfficer extends EditRecord
{
    protected static string $resource = ExtensionOfficerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $newPassword = $this->data['password'] ?? null;
        if (filled($newPassword)) {
            $officer = $this->record;
            $linkedUser = User::where('roleId', $officer->id)
                ->where('role', UserRole::EXTENSION_OFFICER)
                ->first();

            if ($linkedUser) {
                $linkedUser->password = Hash::make($newPassword);
                $linkedUser->save();
            }

            Notification::make()
                ->title('Password updated')
                ->body("Login password for extension officer {$officer->firstName} was updated.")
                ->success()
                ->send();
        }
    }
}
