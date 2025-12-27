<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;

class Dryoff extends Model
{
    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'eventDate',
        'startDate',
        'endDate',
        'reason',
        'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $dryoff): void {
            if (empty($dryoff->uuid)) {
                $dryoff->uuid = UuidHelper::generate();
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
}

