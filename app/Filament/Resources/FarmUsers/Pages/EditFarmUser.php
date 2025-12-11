<?php

namespace App\Filament\Resources\FarmUsers\Pages;

use App\Filament\Resources\FarmUsers\FarmUserResource;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Mail\FarmUserInvitationMail;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EditFarmUser extends EditRecord
{
    protected static string $resource = FarmUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    // Delete linked User account before deleting FarmUser
                    $this->deleteLinkedUserAccount();
                })
                ->after(function () {
                    // Redirect to list page after deletion
                    $this->redirect(FarmUserResource::getUrl('index'));
                }),
        ];
    }

    /**
     * After updating the FarmUser, sync the linked User account.
     */
    protected function afterSave(): void
    {
        $farmUser = $this->record;

        // Find linked User account
        $linkedUser = User::where('role', UserRole::FARM_INVITED_USER)
            ->where('roleId', $farmUser->id)
            ->first();

        if ($linkedUser) {
            // Update User account with new email and username if changed
            $newEmail = $farmUser->email;
            $oldEmail = $linkedUser->email;

            // Update username based on lastName or email prefix
            $username = '';
            if (!empty($farmUser->lastName)) {
                $username = $farmUser->lastName;
            } elseif (!empty($newEmail) && str_contains($newEmail, '@')) {
                $username = strstr($newEmail, '@', true);
            } else {
                $username = $newEmail;
            }

            // Ensure username is unique
            $baseUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->where('id', '!=', $linkedUser->id)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            $linkedUser->username = $username;
            $linkedUser->email = $newEmail;
            $linkedUser->status = UserStatus::ACTIVE;
            $linkedUser->updatedBy = Auth::id();
            $linkedUser->save();

            // If email changed, update password hash and resend invitation email
            if ($newEmail !== $oldEmail) {
                $linkedUser->password = Hash::make($newEmail);
                $linkedUser->save();

                // Resend invitation email with new credentials
                try {
                    Mail::to($newEmail)->send(
                        new FarmUserInvitationMail($farmUser, $newEmail, $newEmail)
                    );
                } catch (\Throwable $mailException) {
                    // Do not break UI flow if email fails
                }
            }

            Notification::make()
                ->title('Farm User updated')
                ->body("Linked User account has been updated for {$farmUser->firstName}.")
                ->success()
                ->send();
        }
    }

    /**
     * Redirect to list page after save to refresh the view.
     */
    protected function getRedirectUrl(): ?string
    {
        return FarmUserResource::getUrl('index');
    }

    /**
     * Delete the linked User account before deleting FarmUser.
     */
    protected function deleteLinkedUserAccount(): void
    {
        $farmUser = $this->record;

        // Find and delete linked User account
        $linkedUser = User::where('role', UserRole::FARM_INVITED_USER)
            ->where('roleId', $farmUser->id)
            ->first();

        if ($linkedUser) {
            $userEmail = $linkedUser->email;
            $linkedUser->delete();

            Notification::make()
                ->title('User account deleted')
                ->body("Linked login account ({$userEmail}) has been deleted.")
                ->success()
                ->send();
        }
    }
}
