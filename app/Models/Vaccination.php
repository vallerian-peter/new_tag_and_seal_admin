<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    protected $fillable = [
        'uuid',
        'vaccinationNo',
        'farmUuid',
        'livestockUuid',
        'vaccineUuid',
        'diseaseId',
        'vetId',
        'extensionOfficerId',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $vaccination): void {
            if (empty($vaccination->uuid)) {
                $vaccination->uuid = UuidHelper::generate();
            }
        });
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }

    public function livestock()
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'vaccineUuid', 'uuid');
    }

    public function disease()
    {
        return $this->belongsTo(Disease::class, 'diseaseId');
    }

    public function vet()
    {
        return $this->belongsTo(Vet::class, 'vetId', 'medicalLicenseNo');
    }

    public function extensionOfficer()
    {
        return $this->belongsTo(ExtensionOfficer::class, 'extensionOfficerId', 'medicalLicenseNo');
    }
}

