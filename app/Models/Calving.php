<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calving extends Model
{
    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'eventDate',
        'startDate',
        'endDate',
        'calvingTypeId',
        'calvingProblemsId',
        'reproductiveProblemId',
        'remarks',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $calving): void {
            if (empty($calving->uuid)) {
                $calving->uuid = UuidHelper::generate();
            }
        });
    }

    public function calvingType(): BelongsTo
    {
        return $this->belongsTo(CalvingType::class, 'calvingTypeId');
    }

    public function calvingProblem(): BelongsTo
    {
        return $this->belongsTo(CalvingProblem::class, 'calvingProblemsId');
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
}

