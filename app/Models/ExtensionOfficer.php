<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtensionOfficer extends Model
{
    protected $table = 'extension_officers';

    protected $fillable = [
        'referenceNo',
        'medicalLicenseNo',
        'fullName',
        'phoneNumber',
        'email',
        'address',
        'countryId',
        'regionId',
        'districtId',
        'gender',
        'dateOfBirth',
        'identityCardTypeId',
        'identityNo',
        'schoolLevelId',
        'status',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'countryId');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'regionId');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'districtId');
    }

    public function identityCardType()
    {
        return $this->belongsTo(IdentityCardType::class, 'identityCardTypeId');
    }

    public function schoolLevel()
    {
        return $this->belongsTo(SchoolLevel::class, 'schoolLevelId');
    }
}


