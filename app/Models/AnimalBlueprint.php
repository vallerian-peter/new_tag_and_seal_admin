<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AnimalBlueprint extends Model
{
    protected $guarded = [];

    protected $casts = ['settings_json' => 'array', 'compiled_manifest_json' => 'array', 'published_at' => 'datetime', 'retired_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
        static::saving(function (self $model): void {
            if ($model->exists && $model->getOriginal('status') !== 'draft') {
                throw ValidationException::withMessages(['status' => 'Published blueprints are immutable. Clone a new draft version.']);
            }
            $form = FormDefinition::where('uuid', $model->registration_form_uuid)->where('purpose', 'registration')->where('status', 'published')->first();
            if (! $form) {
                throw ValidationException::withMessages(['registration_form_uuid' => 'Select a published registration form.']);
            }
            $model->registration_form_version = $form->version;
        });
    }

    public function species()
    {
        return $this->belongsTo(AnimalSpecies::class, 'species_uuid', 'uuid');
    }

    public function events()
    {
        return $this->hasMany(EventDefinition::class, 'blueprint_uuid', 'uuid');
    }
}
