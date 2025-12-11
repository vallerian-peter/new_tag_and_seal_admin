<?php

namespace App\Filament\Resources\FarmUsers\Pages;

use App\Filament\Resources\FarmUsers\FarmUserResource;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Mail\FarmUserInvitationMail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateFarmUser extends CreateRecord
{
    protected static string $resource = FarmUserResource::class;

    /**
     * After creating the FarmUser profile, automatically create
     * the corresponding auth User account (farmInvitedUser).
     */
    protected function afterCreate(): void
    {
        $farmUser = $this->record;

        // Username priority: lastname -> email prefix -> email
        $email = (string) $farmUser->email;
        $username = '';

        if (!empty($farmUser->lastName)) {
            $username = $farmUser->lastName;
        } elseif ($email !== '' && str_contains($email, '@')) {
            $username = strstr($email, '@', true);
        } else {
            $username = $email;
        }

        $currentAdmin = Auth::user();
        $creatorId = $currentAdmin?->id;

        $user = User::create([
            'username'  => $username,
            'email'     => $email,
            // Default password = email (same behavior as AuthController fallback)
            'password'  => Hash::make($email),
            'role'      => UserRole::FARM_INVITED_USER,
            'roleId'    => $farmUser->id,
            'status'    => UserStatus::ACTIVE,
            'createdBy' => $creatorId,
            'updatedBy' => $creatorId,
        ]);

        // Send invitation email with credentials (email + password = email)
        try {
            Mail::to($email)->send(
                new FarmUserInvitationMail($farmUser, $email, $email)
            );
        } catch (\Throwable $mailException) {
            // Do not break UI flow if email fails; optionally could log
        }

        Notification::make()
            ->title('Farm User account created')
            ->body("Login user created for {$farmUser->firstName} ({$user->username}).")
            ->success()
            ->send();
    }
}
