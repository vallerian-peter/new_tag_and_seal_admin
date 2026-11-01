<?php

namespace App\Filament\Resources\AnimalBlueprints\Schemas;

use App\Models\AnimalSpecies;
use App\Models\FormDefinition;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnimalBlueprintForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Animal Classification & Identity')->columns(2)->columnSpanFull()->schema([
                Select::make('settings_json.catalogBinding.livestockTypeId')
                    ->label('Animal Type')
                    ->placeholder('Select Animal Type (e.g. Cattle, Swine, Goat...)')
                    ->options(fn () => DB::table('livestock_types')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('settings_json.catalogBinding.speciesId', null);
                        if ($state) {
                            $typeName = DB::table('livestock_types')->where('id', $state)->value('name');
                            if ($typeName) {
                                if (blank($get('code')) || str_ends_with($get('code') ?? '', '.standard')) {
                                    $set('code', strtolower(Str::slug($typeName)).'.standard');
                                }
                                $matchingSpeciesUuid = AnimalSpecies::where('name', 'like', "%{$typeName}%")->value('uuid');
                                if ($matchingSpeciesUuid) {
                                    $set('species_uuid', $matchingSpeciesUuid);
                                }
                            }
                        }
                    }),

                Select::make('settings_json.catalogBinding.speciesId')
                    ->label('Catalog Species')
                    ->placeholder('Select Species under this Animal Type')
                    ->options(fn (callable $get) => DB::table('species')
                        ->where('livestockTypeId', $get('settings_json.catalogBinding.livestockTypeId'))
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->live(),

                Select::make('species_uuid')
                    ->label('Dynamic Species')
                    ->options(fn () => AnimalSpecies::orderBy('name')->pluck('name', 'uuid'))
                    ->searchable()
                    ->required(),

                TextInput::make('code')
                    ->label('Blueprint Code')
                    ->placeholder('e.g. cattle.standard')
                    ->required()
                    ->regex('/^[a-z][a-z0-9_.]*$/'),

                TextInput::make('version')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->default(1)
                    ->required(),

                Select::make('registration_form_uuid')
                    ->label('Published registration form')
                    ->options(fn () => FormDefinition::where('purpose', 'registration')->where('status', 'published')->orderBy('code')->get()->mapWithKeys(fn ($form) => [$form->uuid => $form->code.' v'.$form->version]))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('registration_form_version', FormDefinition::where('uuid', $state)->value('version'))),

                TextInput::make('registration_form_version')
                    ->disabled()
                    ->dehydrated(false),

                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'retired' => 'Retired'])
                    ->default('draft')
                    ->disabled()
                    ->dehydrated(false),
            ]),

            Section::make('Operational Events Setup')
                ->description('Select standard events (e.g. Feeding, Weight Record, Breeding) to configure for this animal blueprint upon creation.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    CheckboxList::make('initial_events')
                        ->label('Include Events for this Animal Type')
                        ->options([
                            'record_feeding' => 'Feeding (Daily feed type, quantity, and notes)',
                            'weight_record' => 'Weight Record (Growth monitoring and weight gain)',
                            'record_insemination' => 'Insemination / Breeding (Service type and semen details)',
                            'record_pregnancy_test' => 'Pregnancy Test (Gestation result and diagnosis)',
                            'record_transfer' => 'Movement / Transfer (Destination farm / pen)',
                            'record_disposal' => 'Disposal / Sale (Reason and method)',
                        ])
                        ->default(['record_feeding', 'weight_record'])
                        ->columns(2)
                        ->columnSpanFull()
                        ->dehydrated(false),

                    Select::make('event_setup_mode')
                        ->label('Event Form Configuration Mode')
                        ->options([
                            'universal' => 'Universal Standard Forms (Reuse existing schemas)',
                            'clone' => 'Clone & Customize Forms (Create dedicated form version with editable fields)',
                        ])
                        ->default('universal')
                        ->helperText('Universal forms use shared schemas. Cloned forms allow you to customize specific fields and field names.')
                        ->dehydrated(false),
                ]),
            Section::make('Animal management')->columns(2)->columnSpanFull()->schema([
                CheckboxList::make('settings_json.subjectModes')
                    ->label('Registration modes')
                    ->options(['individual' => 'Individual animal', 'group' => 'Group / population'])
                    ->default(['individual'])
                    ->required()
                    ->columns(2),
                CheckboxList::make('settings_json.allowedSexCodes')
                    ->label('Allowed sex values')
                    ->options([
                        'male' => 'Male', 'female' => 'Female', 'intersex' => 'Intersex',
                        'unknown' => 'Unknown', 'not_applicable' => 'Not applicable',
                    ])
                    ->default(['male', 'female', 'unknown'])
                    ->required()
                    ->columns(3),
                Select::make('settings_json.reproductionMode')
                    ->label('Reproduction model')
                    ->options([
                        'live_birth' => 'Live birth', 'egg_laying' => 'Egg laying',
                        'spawning' => 'Spawning', 'colony_split' => 'Colony split',
                        'none' => 'Not tracked',
                    ])
                    ->default('live_birth')
                    ->required(),
                CheckboxList::make('settings_json.locales')
                    ->label('Published languages')
                    ->options(['en' => 'English', 'sw' => 'Kiswahili'])
                    ->default(['en', 'sw'])
                    ->required()
                    ->columns(2),
            ]),
            Section::make('Identification')
                ->description('Barcode and RFID remain the same paired value. Required identifiers must also be allowed.')
                ->columnSpanFull()
                ->schema([
                    CheckboxList::make('settings_json.identificationPolicy.allowed')
                        ->label('Allowed identifiers')
                        ->options(fn () => DB::table('identifier_types')->where('status', 'active')->orderBy('name')->pluck('name', 'code'))
                        ->default(['paired_rfid_barcode'])
                        ->columns(3),
                    CheckboxList::make('settings_json.identificationPolicy.required')
                        ->label('Required identifiers')
                        ->options(fn () => DB::table('identifier_types')->where('status', 'active')->orderBy('name')->pluck('name', 'code'))
                        ->columns(3),
                    Toggle::make('settings_json.identificationPolicy.allowWithoutTag')
                        ->label('Allow registration without an identifier')
                        ->default(true),
                ]),
        ]);
    }
}
