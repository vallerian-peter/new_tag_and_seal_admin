<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'diseaseId',
        'medicineId',
        'quantity',
        'withdrawalPeriod',
        'medicationDate',
        'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $medication): void {
            if (empty($medication->uuid)) {
                $medication->uuid = UuidHelper::generate();
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

    public function disease()
    {
        return $this->belongsTo(Disease::class, 'diseaseId');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicines::class, 'medicineId');
    }
}

