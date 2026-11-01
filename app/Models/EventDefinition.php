<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EventDefinition extends Model
{
    protected $guarded = [];

    protected $casts = [
        'translations_json' => 'array',
        'eligibility_rule_json' => 'array',
        'repeatability_rule_json' => 'array',
        'result_rule_json' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $model) => $model->uuid ??= (string) Str::uuid());
        static::saving(function (self $model): void {
            if ($model->exists && $model->getOriginal('status') !== 'draft') {
                throw ValidationException::withMessages(['status' => 'Published events are immutable. Edit a draft blueprint version.']);
            }
            $blueprint = AnimalBlueprint::where('uuid', $model->blueprint_uuid)->where('status', 'draft')->first();
            $form = FormDefinition::where('uuid', $model->form_uuid)->where('purpose', 'event')->where('status', 'published')->first();
            if (! $blueprint || ! $form) {
                throw ValidationException::withMessages(['form_uuid' => 'Select a draft blueprint and a published event form.']);
            }
            $model->blueprint_version = $blueprint->version;
            $model->form_version = $form->version;
        });
    }

    public function blueprint()
    {
        return $this->belongsTo(AnimalBlueprint::class, 'blueprint_uuid', 'uuid');
    }

    public function familyCode(): ?string
    {
        return $this->family ?? BehaviorDefinition::where('code', $this->behavior_code)->value('family');
    }
}
