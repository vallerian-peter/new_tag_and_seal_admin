<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Farmer extends Model
{
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When a farmer is being deleted, also delete the corresponding user and access codes
        static::deleting(function ($farmer) {
            if ($farmer->email) {
                // Find and delete the user with the same email and farmer role
                \App\Models\User::where('email', $farmer->email)
                    ->where('role', UserRole::FARMER)
                    ->delete();
            }
            
            // Delete all extension officer farm invites (access codes) linked to this farmer
            \App\Models\ExtensionOfficerFarmInvite::where('farmerId', $farmer->id)->delete();
        });
    }
    protected $fillable = [
        'farmerNo',
        'firstName',
        'middleName',
        'surname',
        'phone1',
        'phone2',
        'email',
        'physicalAddress',
        'farmerOrganizationMembership',
        'dateOfBirth',
        'gender',
        'identityCardTypeId',
        'identityNumber',
        'streetId',
        'schoolLevelId',
        'villageId',
        'wardId',
        'districtId',
        'regionId',
        'countryId',
        'farmerType',
        'createdBy',
        'status',
    ];

    public function identityCardType()
    {
        return $this->belongsTo(IdentityCardType::class, 'identityCardTypeId');
    }

    public function street()
    {
        return $this->belongsTo(Street::class, 'streetId');
    }

    public function schoolLevel()
    {
        return $this->belongsTo(SchoolLevel::class, 'schoolLevelId');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'villageId');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'wardId');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'districtId');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'countryId');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }

    public function farms(): HasMany
    {
        return $this->hasMany(Farm::class, 'farmerId');
    }

    public function extensionOfficerInvites(): HasMany
    {
        return $this->hasMany(ExtensionOfficerFarmInvite::class, 'farmerId');
    }
}
