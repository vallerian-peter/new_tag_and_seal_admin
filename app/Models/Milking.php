<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Milking extends Model
{
    protected $fillable = [
        'uuid',
        'livestockUuid',
        'farmUuid',
        'eventDate',
        'milkingMethodId',
        'amount',
        'lactometerReading',
        'solid',
        'solidNonFat',
        'protein',
        'correctedLactometerReading',
        'totalSolids',
        'colonyFormingUnits',
        'acidity',
        'session',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $milking): void {
            if (empty($milking->uuid)) {
                $milking->uuid = UuidHelper::generate();
            }
        });
    }

    public function milkingMethod(): BelongsTo
    {
        return $this->belongsTo(MilkingMethod::class, 'milkingMethodId');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }
}

