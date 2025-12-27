<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pregnancy extends Model
{
    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'eventDate',
        'testResultId',
        'noOfMonths',
        'testDate',
        'status',
        'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $pregnancy): void {
            if (empty($pregnancy->uuid)) {
                $pregnancy->uuid = UuidHelper::generate();
            }
        });
    }

    public function testResult(): BelongsTo
    {
        return $this->belongsTo(TestResults::class, 'testResultId');
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

