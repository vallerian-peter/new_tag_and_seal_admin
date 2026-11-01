<?php

namespace App\Filament\Resources\EventDefinitions\Schemas;

use App\Models\AnimalBlueprint;
use App\Models\FormDefinition;
use App\Services\BehaviorMapping;
use App\Services\EligibilityEditor;
use Closure;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventDefinitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Template Setup (Optional)')
                ->description('Quickly populate this event by reusing or copying from an existing event (e.g. Weight Record, Calving, Vaccination).')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    Select::make('template_event_uuid')
                        ->label('Copy or Reuse Existing Event')
                        ->placeholder('Choose an event template...')
                        ->options(fn () => \App\Models\EventDefinition::orderBy('name')->get()->mapWithKeys(fn ($e) => [$e->uuid => "{$e->name} ({$e->code})"]))
                        ->searchable()
                        ->live()
                        ->dehydrated(false)
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (! $state) return;
                            $source = \App\Models\EventDefinition::where('uuid', $state)->first();
                            if (! $source) return;

                            $set('name', $source->name);
                            $set('code', $source->code);
                            $set('behavior_code', $source->behavior_code);
                            $set('family', $source->family);
                            $set('translations_json.en', $source->translations_json['en'] ?? $source->name);
                            $set('translations_json.sw', $source->translations_json['sw'] ?? null);
                            $set('form_uuid', $source->form_uuid);
                            $set('form_version', $source->form_version);
                            if (isset($source->result_rule_json['legacyLogType'])) {
                                $set('result_rule_json.legacyLogType', $source->result_rule_json['legacyLogType']);
                            }
                        }),
                ]),
            Section::make('Event and form')->description('Attach the event to a draft blueprint and select a published event form. Versions follow your selections.')->columns(2)->columnSpanFull()->schema([
                Select::make('blueprint_uuid')->label('Draft blueprint')->options(fn () => AnimalBlueprint::where('status', 'draft')->orderBy('code')->get()->mapWithKeys(fn ($blueprint) => [$blueprint->uuid => $blueprint->code.' v'.$blueprint->version]))->searchable()->required()->live()->afterStateUpdated(fn ($state, callable $set) => $set('blueprint_version', AnimalBlueprint::where('uuid', $state)->value('version'))),
                TextInput::make('blueprint_version')->disabled()->dehydrated(false),
                TextInput::make('code')->required()->regex('/^[a-z][a-z0-9_]*$/'),
                TextInput::make('name')->label('Event name — English')->required()->live(onBlur: true)->afterStateUpdated(fn ($state, callable $set) => $set('translations_json.en', $state)),
                TextInput::make('translations_json.sw')->label('Event name — Kiswahili')->helperText('Optional. English is used when empty.'),
                Select::make('form_uuid')->label('Published event form')->options(fn () => FormDefinition::where('purpose', 'event')->where('status', 'published')->orderBy('code')->get()->mapWithKeys(fn ($form) => [$form->uuid => $form->code.' v'.$form->version]))->searchable()->required()->live()->afterStateUpdated(fn ($state, callable $set) => $set('form_version', FormDefinition::where('uuid', $state)->value('version'))),
                TextInput::make('form_version')->disabled()->dehydrated(false),
                Select::make('behavior_code')->label('Behaviour')
                    ->options(fn () => app(BehaviorMapping::class)->behaviorOptions())
                    ->searchable()->required()->live()
                    ->helperText('What the platform does when this event is saved, for example update the weight or count births.')
                    ->rule(fn (callable $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                        foreach (app(BehaviorMapping::class)->analyze($get('blueprint_uuid'), $get('form_uuid'), $value, $get('form_version'))['errors'] as $error) {
                            $fail($error);
                        }
                    }),
                Select::make('family')->label('Family')
                    ->helperText('Optional: the family this event reports under and switches with. Empty uses the behaviour\'s family.')
                    ->options(BehaviorMapping::FAMILIES)->placeholder('Same as behaviour'),
                Select::make('result_rule_json.legacyLogType')->label('Existing mobile event action')
                    ->helperText('Optional: use this configured form when users open an existing event action. Leave empty for a new event.')
                    ->options(['feeding' => 'Feeding', 'weightChange' => 'Weight change', 'vaccination' => 'Vaccination', 'deworming' => 'Deworming', 'treatment' => 'Treatment', 'milking' => 'Milking', 'pregnancy' => 'Pregnancy', 'insemination' => 'Insemination', 'dryoff' => 'Dry off', 'disposal' => 'Disposal', 'transfer' => 'Transfer', 'calving' => 'Calving', 'farrowing' => 'Farrowing', 'abortedPregnancy' => 'Aborted pregnancy', 'teethClipping' => 'Teeth clipping', 'tailDocking' => 'Tail docking', 'ironInjection' => 'Iron injection', 'livestockMarking' => 'Marking', 'stageChange' => 'Stage change', 'prepuceCondition' => 'Prepuce condition'])->searchable(),
                TextInput::make('sort_order')->numeric()->integer()->default(0),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'retired' => 'Retired'])->default('draft')->disabled()->dehydrated(false),
            ]),
            Section::make('Behaviour inputs')
                ->description('Each input the behaviour reads must come from one field of the selected form. Mark a field with its behaviour input in the form builder, or use the input name as the field key.')
                ->visible(fn (callable $get): bool => filled($get('behavior_code')) && filled($get('form_uuid')))
                ->columnSpanFull()->schema([
                    ViewField::make('behavior_mapping')->hiddenLabel()->dehydrated(false)
                        ->view('filament.forms.behavior-mapping')
                        ->viewData(fn (callable $get): array => ['mapping' => app(BehaviorMapping::class)->analyze($get('blueprint_uuid'), $get('form_uuid'), $get('behavior_code'), $get('form_version'))]),
                ]),
            Section::make('Event applicability')->description('Choose which animals can use this event. Empty choices add no restriction. Advanced rules are combined with these choices.')->columns(2)->columnSpanFull()->schema([
                CheckboxList::make('preview_sex_codes')->label('Allowed sex values')
                    ->options(['female' => 'Female', 'male' => 'Male', 'unknown' => 'Unknown', 'intersex' => 'Intersex', 'not_applicable' => 'Not applicable'])
                    ->default(['female', 'male'])->columns(3)->dehydrated(false)
                    ->afterStateHydrated(fn ($component, $record) => $component->state(EligibilityEditor::selected($record?->eligibility_rule_json, 'subject.sex_code')))
                    ->mutateDehydratedStateUsing(fn ($state) => $state),
                CheckboxList::make('preview_unit_types')->label('Management modes')
                    ->options(['individual' => 'Individual animal', 'group' => 'Group / population'])
                    ->default(['individual'])->columns(2)->dehydrated(false)
                    ->afterStateHydrated(fn ($component, $record) => $component->state(EligibilityEditor::selected($record?->eligibility_rule_json, 'subject.unit_type'))),
                Textarea::make('eligibility_rule_json')->label('Advanced rules (JSON)')
                    ->rule('json')
                    ->helperText('Combined with choices above. Leave blank for simple rules.')
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $state)
                    ->mutateDehydratedStateUsing(function ($state, callable $get): ?array {
                        $rule = filled($state) ? (is_array($state) ? $state : json_decode($state, true)) : null;
                        $rule = EligibilityEditor::update($rule, 'subject.sex_code', $get('preview_sex_codes') ?? []);
                        return EligibilityEditor::update($rule, 'subject.unit_type', $get('preview_unit_types') ?? []);
                    })->columnSpanFull(),
            ]),
        ]);
    }
}
