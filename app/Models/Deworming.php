<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\UuidHelper;

class Deworming extends Model
{
    protected $table = 'dewormings';

    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'administrationRouteId',
        'medicineId',
        'vetId',
        'extensionOfficerId',
        'quantity',
        'dose',
        'nextAdministrationDate',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $deworming): void {
            if (empty($deworming->uuid)) {
                $deworming->uuid = UuidHelper::generate();
            }
        });
    }

    public function administrationRoute()
    {
        return $this->belongsTo(AdministrationRoute::class, 'administrationRouteId');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicines::class, 'medicineId');
    }

    public function livestock()
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
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


