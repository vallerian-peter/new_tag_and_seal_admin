<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'eventDate',
        'toFarmUuid',
        'transporterId',
        'reason',
        'price',
        'transferDate',
        'remarks',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $transfer): void {
            if (empty($transfer->uuid)) {
                $transfer->uuid = UuidHelper::generate();
            }
        });
    }

    public function fromFarm()
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }

    public function toFarm()
    {
        return $this->belongsTo(Farm::class, 'toFarmUuid', 'uuid');
    }

    public function livestock()
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }
}

