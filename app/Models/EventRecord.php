<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EventRecord extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload_json' => 'array',
        'occurred_at' => 'datetime',
        'voided_at' => 'datetime',
        'revision' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            $model->uuid ??= (string) Str::uuid();
            $model->idempotency_uuid ??= (string) Str::uuid();
        });
    }

    public function subject()
    {
        return $this->belongsTo(AnimalSubject::class, 'subject_uuid', 'uuid');
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_uuid', 'uuid');
    }

    public function definition()
    {
        return $this->belongsTo(EventDefinition::class, 'event_code', 'code');
    }

    public function eventDefinition()
    {
        return $this->belongsTo(EventDefinition::class, 'event_code', 'code');
    }

    public function behavior()
    {
        return $this->belongsTo(BehaviorDefinition::class, 'behavior_code', 'code');
    }

    public function familyCode(): ?string
    {
        return $this->behavior?->family ?? $this->definition?->familyCode() ?? BehaviorDefinition::where('code', $this->behavior_code)->value('family');
    }
}
