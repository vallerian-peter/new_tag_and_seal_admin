<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BirthEvent extends Model
{
    protected $table = 'birth_events';

    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'eventType', // 'calving' or 'farrowing'
        'startDate',
        'endDate',
        'birthTypeId',
        'birthProblemsId',
        'reproductiveProblemId',
        'remarks',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $birthEvent): void {
            if (empty($birthEvent->uuid)) {
                $birthEvent->uuid = UuidHelper::generate();
            }
        });
    }

    public function birthType(): BelongsTo
    {
        return $this->belongsTo(BirthType::class, 'birthTypeId');
    }

    public function birthProblem(): BelongsTo
    {
        return $this->belongsTo(BirthProblem::class, 'birthProblemsId');
    }

    // Keep old method names for backward compatibility (deprecated)
    public function calvingType(): BelongsTo
    {
        return $this->birthType();
    }

    public function calvingProblem(): BelongsTo
    {
        return $this->birthProblem();
    }

    public function reproductiveProblem(): BelongsTo
    {
        return $this->belongsTo(ReproductiveProblem::class, 'reproductiveProblemId');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }

    /**
     * Get display name based on event type
     */
    public function getEventNameAttribute(): string
    {
        return $this->eventType === 'farrowing' ? 'Farrowing' : 'Calving';
    }

    /**
     * Get offspring name based on event type
     */
    public function getOffspringNameAttribute(): string
    {
        return $this->eventType === 'farrowing' ? 'Piglet' : 'Calf';
    }
}

