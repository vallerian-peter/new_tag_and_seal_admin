<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $existingUser = User::where('username', 'admin')->first();
        if ($existingUser) {
            $this->command->warn('Admin user already exists!');
            $this->command->info('Username: admin');
            $this->command->info('Email: ' . $existingUser->email);
            $this->command->info('Password: password (if not changed)');
            return;
        }

        // Disable foreign key checks temporarily
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            // Create a temporary SystemUser profile with dummy createdBy
            $systemUser = SystemUser::create([
                'firstName' => 'Admin',
                'middleName' => null,
                'lastName' => 'User',
                'phone' => '+255712345678',
                'address' => 'Dar es Salaam, Tanzania',
                'createdBy' => 0, // Temporary value, will be updated
            ]);

            // Create the User account with systemUser role
            $user = User::create([
                'username' => 'admin',
                'email' => 'admin@tagandseals.com',
                'password' => Hash::make('password'), // Change this in production!
                'role' => UserRole::SYSTEM_USER,
                'roleId' => $systemUser->id,
                'status' => UserStatus::ACTIVE,
                'createdBy' => 0, // Temporary value
                'updatedBy' => null,
            ]);

            // Update systemUser and user createdBy to point to the created user
            $systemUser->update(['createdBy' => $user->id]);
            $user->update(['createdBy' => $user->id]);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Username: admin');
            $this->command->info('Email: admin@tagandseals.com');
            $this->command->info('Password: password');
            $this->command->info('Role: System User');
        } finally {
            // Re-enable foreign key checks
            \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}

