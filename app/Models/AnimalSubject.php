<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnimalSubject extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'attributes_json' => 'array',
        'revision' => 'integer',
        'quantity' => 'integer',
        'birth_or_start_date' => 'date',
        'restored_at' => 'datetime',
    ];

    public function species(): BelongsTo
    {
        return $this->belongsTo(AnimalSpecies::class, 'species_uuid', 'uuid');
    }

    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(AnimalBlueprint::class, 'blueprint_uuid', 'uuid');
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class, 'farm_uuid', 'uuid');
    }
}
