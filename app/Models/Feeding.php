<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\UuidHelper;

class Feeding extends Model
{
    protected $fillable = [
        'uuid',
        'feedingTypeId',
        'farmUuid',
        'livestockUuid',
        'nextFeedingTime',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'nextFeedingTime' => 'string',
        'amount' => 'string',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $feeding): void {
            if (empty($feeding->uuid)) {
                $feeding->uuid = UuidHelper::generate();
            }
        });
    }

    public function feedingType()
    {
        return $this->belongsTo(FeedingType::class, 'feedingTypeId');
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }

    public function livestock()
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }
}


