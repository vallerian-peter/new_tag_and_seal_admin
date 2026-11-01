<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnimalSpecies extends Model
{
    protected $guarded = [];

    protected $casts = ['translations_json' => 'array', 'capabilities_json' => 'array', 'published_at' => 'datetime', 'retired_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
    }
}
