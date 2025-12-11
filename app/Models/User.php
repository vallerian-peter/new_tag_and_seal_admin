<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'roleId',
        'status',
        'createdBy',
        'updatedBy',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the role of the user.
     */
    // public function getRoleAttribute(): string
    // {
    //     return $this->role;
    // }

    /**
     * Check if user is a system user.
     */
    public function isSystemUser(): bool
    {
        return $this->role === UserRole::SYSTEM_USER;
    }

    /**
     * Check if user is a farmer.
     */
    public function isFarmer(): bool
    {
        return $this->role === UserRole::FARMER;
    }

    /**
     * Check if user is an extension officer.
     */
    public function isExtensionOfficer(): bool
    {
        return $this->role === UserRole::EXTENSION_OFFICER;
    }

    /**
     * Check if user is a vet.
     */
    public function isVet(): bool
    {
        return $this->role === UserRole::VET;
    }

    /**
     * Check if user is a farm invited user.
     */
    public function isFarmInvitedUser(): bool
    {
        return $this->role === UserRole::FARM_INVITED_USER;
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    /**
     * Check if user has one of the specified roles.
     */
    public function hasRole(string|array $roles): bool
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is an admin (system user).
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::admins());
    }

    /**
     * Check if user is a field worker.
     */
    public function isFieldWorker(): bool
    {
        return $this->hasRole(UserRole::fieldWorkers());
    }

    /**
     * Check if user can manage livestock.
     */
    public function canManageLivestock(): bool
    {
        return $this->hasRole(UserRole::livestockManagers());
    }

    /**
     * Get the creator of this user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }

    /**
     * Get the updater of this user.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updatedBy');
    }

    /**
     * Scope to filter by role.
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter active users.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Find user by username or email.
     */
    public static function findByUsernameOrEmail(string $login): ?self
    {
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        return self::where($field, $login)->first();
    }

    /**
     * Get the profile data based on role.
     */
    public function profile()
    {
        switch ($this->role) {
            case UserRole::FARMER:
                return $this->belongsTo(Farmer::class, 'roleId');
            case UserRole::SYSTEM_USER:
            case UserRole::EXTENSION_OFFICER:
            case UserRole::VET:
            case UserRole::FARM_INVITED_USER:
                return $this->belongsTo(SystemUser::class, 'roleId');
            default:
                return null;
        }
    }

    /**
     * Get role display name.
     */
    public function getRoleDisplayName(): string
    {
        return UserRole::getDisplayName($this->role);
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayName(): string
    {
        return UserStatus::getDisplayName($this->status);
    }

    /**
     * Determine if the user can access the Filament admin panel.
     * Only systemUser role can access the admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Only allow system users to access the admin panel
        return $this->role === UserRole::SYSTEM_USER && $this->status === UserStatus::ACTIVE;
    }

    /**
     * Get the name that should be displayed in Filament.
     * Load name from the respective profile table based on user role.
     */
    public function getFilamentName(): string
    {
        // Check if profile relationship exists before trying to load it
        $profileRelation = $this->profile();

        if ($profileRelation === null) {
            return $this->username ?? $this->email ?? 'User';
        }

        // Try to get name from the profile based on role
        $profile = $profileRelation->first();

        if ($profile) {
            // If it's a Farmer profile
            if ($this->role === UserRole::FARMER) {
                $name = trim($profile->firstName . ' ' . ($profile->middleName ?? '') . ' ' . $profile->surname);
                return $name ?: ($this->username ?? $this->email ?? 'User');
            }

            // If it's a SystemUser profile (systemUser, extensionOfficer, vet, farmInvitedUser)
            if (in_array($this->role, [UserRole::SYSTEM_USER, UserRole::EXTENSION_OFFICER, UserRole::VET, UserRole::FARM_INVITED_USER])) {
                $name = trim($profile->firstName . ' ' . ($profile->middleName ?? '') . ' ' . $profile->lastName);
                return $name ?: ($this->username ?? $this->email ?? 'User');
            }
        }

        // Fallback to username or email if profile not loaded
        return $this->username ?? $this->email ?? 'User';
    }
}
