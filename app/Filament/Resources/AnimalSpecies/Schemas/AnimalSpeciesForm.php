<?php

namespace App\Filament\Resources\AnimalSpecies\Schemas;

use App\Filament\Resources\AnimalSpecies\AnimalSpeciesResource;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;

class AnimalSpeciesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->required()
                ->regex('/^[a-z][a-z0-9_]*$/')
                ->unique(ignoreRecord: true),
            TextInput::make('name')
                ->label('Name — English')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set) => $set('translations_json.en', $state)),
            Select::make('default_subject_mode')
                ->options([
                    'individual' => 'Individual',
                    'group' => 'Group',
                    'both' => 'Individual and group',
                ])
                ->required(),
            TextInput::make('group_display_name')
                ->helperText('Examples: herd, flock, colony, batch, population.'),
            TextInput::make('category_code'),
            TextInput::make('translations_json.sw')
                ->label('Name — Kiswahili')
                ->helperText('Optional. English is used when empty.'),
            Select::make('status')
                ->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'retired' => 'Retired',
                ])
                ->default('draft')
                ->disabled()
                ->dehydrated(),
            Section::make('Capabilities')
                ->description('Behaviours check these when a blueprint is published, for example births need live birth and egg records need egg laying.')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    Select::make('legacy_livestock_type_id')
                        ->label('Existing mobile livestock type')
                        ->options(fn () => DB::table('livestock_types')->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->unique(ignoreRecord: true)
                        ->helperText('Links this species to the livestock type already used by the mobile app, so both read the same animals.'),
                    Select::make('capabilities_json.reproduction')
                        ->label('Reproduction')
                        ->options(AnimalSpeciesResource::REPRODUCTION)
                        ->default('none')
                        ->required(),
                    CheckboxList::make('capabilities_json.subjectModes')
                        ->label('Management modes')
                        ->options([
                            'individual' => 'Individual animal',
                            'group' => 'Group / population',
                        ])
                        ->required()
                        ->columns(2),
                    TagsInput::make('capabilities_json.products')
                        ->label('Products')
                        ->placeholder('milk, eggs, meat, honey')
                        ->nestedRecursiveRules(['regex:/^[a-z][a-z0-9_]*$/']),
                ]),
        ]);
    }
}
