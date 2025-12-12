<?php

namespace App\Filament\Resources\SystemUsers\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\SystemUsers\SystemUserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSystemUser extends EditRecord
{
    protected static string $resource = SystemUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    // Delete linked User accounts before deleting SystemUser
                    $this->deleteLinkedUserAccounts();
                }),
        ];
    }

    /**
     * Delete linked User accounts before deleting SystemUser.
     */
    protected function deleteLinkedUserAccounts(): void
    {
        $systemUser = $this->record;

        // Check if this SystemUser has an admin (SYSTEM_USER) account
        $adminUser = User::where('roleId', $systemUser->id)
            ->where('role', UserRole::SYSTEM_USER)
            ->first();

        if ($adminUser) {
            // Count total admin users
            $totalAdmins = User::where('role', UserRole::SYSTEM_USER)->count();

            // Prevent deletion if this would leave less than 2 admins
            if ($totalAdmins <= 1) {
                Notification::make()
                    ->title('Cannot delete admin')
                    ->body('You cannot delete the last admin user. At least 2 admin users must exist in the system.')
                    ->danger()
                    ->persistent()
                    ->send();

                // Prevent deletion by throwing an exception
                throw new \Exception('Cannot delete the last admin user.');
            }
        }

        // Find Users where roleId matches SystemUser id and role uses SystemUser profile
        $linkedUsers = User::where('roleId', $systemUser->id)
            ->whereIn('role', [
                UserRole::SYSTEM_USER,
                UserRole::EXTENSION_OFFICER,
                UserRole::VET,
            ])
            ->get();

        if ($linkedUsers->isNotEmpty()) {
            $emails = $linkedUsers->pluck('email')->filter()->toArray();
            
            foreach ($linkedUsers as $user) {
                $user->delete();
            }

            $emailList = !empty($emails) ? implode(', ', $emails) : 'N/A';
            Notification::make()
                ->title('User accounts deleted')
                ->body("Linked login account(s) ({$emailList}) have been deleted.")
                ->success()
                ->send();
        }
    }
}
