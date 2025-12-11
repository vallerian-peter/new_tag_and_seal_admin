<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbortedPregnancy extends Model
{
    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'abortionDate',
        'reproductiveProblemId',
        'remarks',
        'status', // 0: Pending, 1: Approved, 2: Rejected
    ];

    protected $casts = [
        'abortionDate' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $abortedPregnancy): void {
            if (empty($abortedPregnancy->uuid)) {
                $abortedPregnancy->uuid = UuidHelper::generate();
            }
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }

    public function reproductiveProblem(): BelongsTo
    {
        return $this->belongsTo(ReproductiveProblem::class, 'reproductiveProblemId');
    }
}

