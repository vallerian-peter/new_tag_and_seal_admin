<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FormDefinition extends Model
{
    protected $guarded = [];

    protected $casts = ['schema_json' => 'array', 'published_at' => 'datetime', 'retired_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
        static::updating(function (self $model): void {
            if ($model->getOriginal('status') === 'published') {
                throw new \LogicException('Published forms are immutable. Create a new version.');
            }
        });
    }
}
