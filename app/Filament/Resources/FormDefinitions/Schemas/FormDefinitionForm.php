<?php

namespace App\Filament\Resources\FormDefinitions\Schemas;

use App\Filament\Components\LocalizedText;
use App\Models\LookupSet;
use App\Services\BehaviorCatalog;
use App\Services\DynamicConfigurationService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FormDefinitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Form identity')->columns(2)->columnSpanFull()->schema([
                TextInput::make('code')->required()->regex('/^[a-z][a-z0-9_.]*$/')->helperText('Unique lowercase identifier, for example cattle.registration.'),
                TextInput::make('version')->numeric()->integer()->minValue(1)->default(1)->required(),
                Select::make('purpose')->options(['registration' => 'Registration form', 'event' => 'Event form'])->default('event')->required(),
                Select::make('status')->options(['draft' => 'Draft', 'published' => 'Published', 'retired' => 'Retired'])->default('draft')->disabled()->dehydrated(false),
            ]),
            Section::make('Form presentation')->columns(2)->columnSpanFull()->schema([
                LocalizedText::make('schema_json.presentation.title', 'Form title'),
                LocalizedText::make('schema_json.presentation.description', 'Form description', false, true),
                LocalizedText::make('schema_json.presentation.submitLabel', 'Submit button label'),
            ]),
            Section::make('Form sections')->columnSpanFull()->schema([
                Repeater::make('schema_json.sections')->label('Sections in order')
                    ->defaultItems(1)->addActionLabel('Add section')->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                    ->schema([
                        TextInput::make('key')->required()->regex('/^[a-z][a-z0-9_]*$/')->helperText('Section key in JSON.'),
                        LocalizedText::make('label', 'Section heading'),
                        Repeater::make('fields')->label('Fields in this section')
                            ->defaultItems(0)->addActionLabel('Add field')->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                            ->schema([
                                Section::make('Field details')->columnSpanFull()->columns(2)->schema([
                                    TextInput::make('key')->required()->regex('/^[a-z][a-z0-9_]*$/')->helperText('Stored identifier for this field in JSON. Lowercase letters, numbers, and underscores only.'),
                                    LocalizedText::make('label', 'Field name shown to users'),
                            LocalizedText::make('help', 'Help text shown under field', false),
                            Select::make('required')->options(['always' => 'Required', 'optional' => 'Optional'])->default('optional')->required(),
                            Select::make('component')->label('Input component')->required()->live()
                                ->options(array_intersect_key([
                                    'text' => 'Short text', 'multiline_text' => 'Long text', 'integer' => 'Whole number',
                                    'decimal' => 'Decimal number', 'boolean' => 'Yes / No', 'date' => 'Date',
                                    'date_time' => 'Date and time', 'time' => 'Time of day', 'single_select' => 'Single choice',
                                    'multi_select' => 'Multiple choices', 'measurement' => 'Measurement with unit',
                                    'subject_reference' => 'Another registered animal',
                                    'repeatable_group' => 'Repeatable entries (e.g. offspring)',
                                    'photo' => 'Photo', 'location' => 'GPS coordinates',
                                ], array_flip(DynamicConfigurationService::MOBILE_COMPONENTS)))
                                ->afterStateUpdated(function (callable $set, $state): void {
                                    $set('value_type', match ($state) {
                                        'integer' => 'integer', 'decimal', 'measurement' => 'decimal',
                                        'boolean' => 'boolean', 'date' => 'date', 'date_time' => 'date_time',
                                        'multi_select', 'repeatable_group' => 'array',
                                        'location' => 'object', default => 'string',
                                    });
                                })->helperText('Unavailable input types are not yet supported by the mobile app and cannot be published.'),
                            Select::make('unit')
                                ->label('Measurement unit')
                                ->options(DynamicConfigurationService::MEASUREMENT_UNITS)
                                ->visible(fn (callable $get): bool => $get('component') === 'measurement')
                                ->required(fn (callable $get): bool => $get('component') === 'measurement')
                                ->helperText('Stored with every value. The mobile app shows the unit beside the input.'),
                            Select::make('value_type')->options([
                                'string' => 'Text', 'integer' => 'Whole number',
                                'decimal' => 'Decimal', 'boolean' => 'Yes / No',
                                'date' => 'Date', 'date_time' => 'Date and time',
                                'array' => 'List', 'object' => 'Object',
                            ])->required(),
                            Select::make('core_binding')->label('Value from an existing input')->options(['name' => 'Animal name', 'breedName' => 'Selected breed name', 'gender' => 'Selected sex', 'sexCode' => 'Selected sex code', 'identificationNumber' => 'Identification number', 'dateOfBirth' => 'Birth / start date', 'dateFirstEnteredToFarm' => 'Date entered farm', 'weightAsOnRegistration' => 'Registration weight', 'stageId' => 'Selected stage', 'motherUuid' => 'Selected mother', 'fatherUuid' => 'Selected father', 'primaryColor' => 'Primary colour', 'secondaryColor' => 'Secondary colour', 'isIdentified' => 'Identification status', 'newWeight' => 'New recorded weight', 'oldWeight' => 'Previous weight', 'vaccineUuid' => 'Selected vaccine', 'diseaseId' => 'Selected disease', 'remarks' => 'Remarks', 'eventDate' => 'Event date', 'quantity' => 'Group quantity'])->searchable()->helperText('Optional. Reuse an existing input instead of asking for the same value twice. Leave empty for a new configured field.'),
                            Select::make('role')->label('Behaviour input')
                                ->options(fn () => self::roleOptions())->searchable()
                                ->helperText('Optional. Tells an event behaviour which value to read, for example born alive for births. Fields named like the input are matched automatically.'),
                            Section::make('Animal reference')->columnSpanFull()->columns(2)
                                ->visible(fn (callable $get): bool => $get('component') === 'subject_reference')
                                ->schema([
                                    Toggle::make('reference.same_species')->label('Same species only')->default(true),
                                    Toggle::make('reference.multiple')->label('Allow several animals')->default(false),
                                    CheckboxList::make('reference.sex_codes')->label('Allowed sex')->columns(2)
                                        ->options(['female' => 'Female', 'male' => 'Male', 'unknown' => 'Unknown', 'intersex' => 'Intersex', 'not_applicable' => 'Not applicable']),
                                    CheckboxList::make('reference.unit_types')->label('Management modes')->columns(2)
                                        ->options(['individual' => 'Individual animal', 'group' => 'Group / population']),
                                ]),
                            Repeater::make('item_fields')->label('Fields in each entry')
                                ->visible(fn (callable $get): bool => $get('component') === 'repeatable_group')
                                ->defaultItems(0)->addActionLabel('Add entry field')->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['key'] ?? null)
                                ->schema([
                                    TextInput::make('key')->required()->regex('/^[a-z][a-z0-9_]*$/'),
                                    LocalizedText::make('label', 'Field name shown to users'),
                                    Select::make('component')->required()->live()->options(array_intersect_key([
                                        'text' => 'Short text', 'multiline_text' => 'Long text', 'integer' => 'Whole number',
                                        'decimal' => 'Decimal number', 'date' => 'Date', 'boolean' => 'Yes / No',
                                        'single_select' => 'Single choice', 'measurement' => 'Measurement',
                                    ], array_flip(DynamicConfigurationService::ITEM_COMPONENTS))),
                                    Select::make('unit')->label('Measurement unit')->options(DynamicConfigurationService::MEASUREMENT_UNITS)
                                        ->visible(fn (callable $get): bool => $get('component') === 'measurement')
                                        ->required(fn (callable $get): bool => $get('component') === 'measurement'),
                                    Select::make('required')->options(['always' => 'Required', 'optional' => 'Optional'])->default('optional')->required(),
                                    Select::make('role')->label('Behaviour input')->options(fn () => self::roleOptions())->searchable(),
                                    Select::make('core_binding')->label('Value from existing input')->options(['name' => 'Name', 'gender' => 'Sex', 'sexCode' => 'Sex code', 'identificationNumber' => 'Tag ID', 'weightAsOnRegistration' => 'Weight', 'primaryColor' => 'Primary colour', 'isIdentified' => 'Identified'])->searchable(),
                                    Select::make('lookup_code')->label('Choice list')->visible(fn (callable $get): bool => $get('component') === 'single_select')->options(fn () => LookupSet::where('status', 'published')->orderBy('name')->pluck('name', 'code'))->searchable(),
                                ])->columns(2)->columnSpanFull(),
                            Select::make('lookup_code')
                                ->label('Choice list')
                                ->visible(fn (callable $get): bool => in_array($get('component'), ['single_select', 'multi_select'], true))
                                ->options(fn () => LookupSet::where('status', 'published')->orderBy('name')->pluck('name', 'code'))
                                ->searchable()->live()->helperText('Create choice lists under System & Configuration → Lookup Sets before selecting them here.'),
                            Select::make('lookup_parent_field')
                                ->label('Filter using earlier field')
                                ->visible(fn (callable $get): bool => in_array($get('component'), ['single_select', 'multi_select'], true) && filled($get('lookup_code')))
                                ->options(function (callable $get): array {
                                    $fields = $get('../../fields') ?? [];
                                    return collect($fields)->filter(fn ($field) => is_array($field) && filled($field['key'] ?? null))->mapWithKeys(fn ($field) => [$field['key'] => $field['label']['en'] ?? $field['key']])->all();
                                })
                                ->searchable()->nullable()->helperText('For a dependent lookup such as District, select the Region field. Options are filtered offline.'),
                            KeyValue::make('validation')
                                ->helperText('Supported keys: min, max (numbers), min_length, max_length (text), min_items, max_items (repeatable groups). Publishing validates contradictions.'),
                            Section::make('Validation wording')->collapsed()->columnSpanFull()->schema([
                                LocalizedText::make('validation_messages.REQUIRED', 'Required value message', false),
                                LocalizedText::make('validation_messages.MINIMUM', 'Minimum value message', false),
                                LocalizedText::make('validation_messages.MAXIMUM', 'Maximum value message', false),
                                LocalizedText::make('validation_messages.INVALID_TYPE', 'Invalid value type message', false),
                                LocalizedText::make('validation_messages.NOT_ALLOWED', 'Invalid choice message', false),
                                LocalizedText::make('validation_messages.MINIMUM_LENGTH', 'Text too short message', false),
                                LocalizedText::make('validation_messages.MAXIMUM_LENGTH', 'Text too long message', false),
                                LocalizedText::make('validation_messages.MINIMUM_ITEMS', 'Too few items message', false),
                                LocalizedText::make('validation_messages.MAXIMUM_ITEMS', 'Too many items message', false),
                                LocalizedText::make('validation_messages.LOOKUP_PARENT_MISMATCH', 'Dependent choice mismatch message', false),
                                LocalizedText::make('validation_messages.LOOKUP_UNAVAILABLE', 'Lookup unavailable message', false),
                                LocalizedText::make('validation_messages.UNSUPPORTED_COMPONENT', 'App update required message', false),
                            ]),
                            TextInput::make('sort_order')->integer()->default(0)->required(),
                        ]),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                ]),
            ]),
        ]);
    }

    public static function roleOptions(): array
    {
        $options = [];
        foreach (app(BehaviorCatalog::class)->all() as $behavior) {
            foreach ($behavior->roles_json as $role) {
                $options[$role['role']] ??= ['label' => $role['label']['en'] ?? $role['role'], 'behaviors' => []];
                $options[$role['role']]['behaviors'][] = $behavior->translations_json['en'] ?? $behavior->code;
            }
        }
        ksort($options);

        return array_map(fn (array $option) => $option['label'].' — '.implode(', ', $option['behaviors']), $options);
    }
}
