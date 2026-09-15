<?php

namespace App\Filament\Resources\Livestocks\Schemas;

use App\Support\UuidHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class LivestockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('farmUuid')
                    ->label('Farm')
                    ->relationship('farm', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?: ($record->referenceNo ?: 'Farm #' . $record->id)) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Hidden::make('uuid')
                    ->default(fn () => UuidHelper::generate())
                    ->required(),
                TextInput::make('identificationNumber')
                    ->nullable(),
                TextInput::make('dummyTagId')
                    ->default(null),
                TextInput::make('barcodeTagId')
                    ->default(null),
                TextInput::make('rfidTagId')
                    ->default(null),
                Select::make('livestockTypeId')
                    ->label('Livestock Type')
                    ->relationship('livestockType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required(),
                DatePicker::make('dateOfBirth')
                    ->required(),
                Select::make('motherUuid')
                    ->label('Mother')
                    ->relationship('mother', 'identificationNumber')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->identificationNumber ?: ($record->name ?: 'Livestock #' . $record->id)) . ' ' . ($record->name ? "- {$record->name}" : '')))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('fatherUuid')
                    ->label('Father')
                    ->relationship('father', 'identificationNumber')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->identificationNumber ?: ($record->name ?: 'Livestock #' . $record->id)) . ' ' . ($record->name ? "- {$record->name}" : '')))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('birthEventUuid')
                    ->label('Birth Event')
                    ->relationship('birthEventAsOffspring', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => 'Birth Event: ' . ($record->eventDate ?? $record->created_at?->format('Y-m-d') ?? '#' . $record->id))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('stageId')
                    ->label('Stage')
                    ->relationship('stage', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('isIdentified')
                    ->label('Identified')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->default(1)
                    ->required(),
                Select::make('gender')
                    ->options(['male' => 'Male', 'female' => 'Female'])
                    ->required(),
                Select::make('breedId')
                    ->label('Breed')
                    ->relationship('breed', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('speciesId')
                    ->label('Species')
                    ->relationship('species', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->options(['active' => 'Active', 'notActive' => 'Not active'])
                    ->default('active')
                    ->required(),
                Select::make('livestockObtainedMethodId')
                    ->label('Obtained Method')
                    ->relationship('livestockObtainedMethod', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                DatePicker::make('dateFirstEnteredToFarm'),
                TextInput::make('weightAsOnRegistration')
                    ->default(null),
                Select::make('primaryColor')
                    ->label('Primary Color')
                    ->options(fn (Get $get): array => self::getColorOptions(currentColor: $get('primaryColor')))
                    ->searchable()
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                        if ($state && $state === $get('secondaryColor')) {
                            $set('secondaryColor', null);
                        }
                    }),
                Select::make('secondaryColor')
                    ->label('Secondary Color')
                    ->options(fn (Get $get): array => self::getColorOptions(exclude: $get('primaryColor'), currentColor: $get('secondaryColor')))
                    ->searchable()
                    ->nullable()
                    ->live(),
            ]);
    }

    /**
     * Get color options for dropdown, aligned with the mobile app (ColorHelper)
     */
    public static function getColorOptions(?string $exclude = null, ?string $currentColor = null): array
    {
        $colors = [
            'black' => 'Black',
            'white' => 'White',
            'brown' => 'Brown',
            'red' => 'Red',
            'gray' => 'Gray',
        ];

        // Keep current color if an existing record has a custom/legacy value
        if ($currentColor && !isset($colors[$currentColor])) {
            $colors[$currentColor] = ucfirst($currentColor);
        }

        // Exclude the selected primary color so it does not appear in secondary color
        if ($exclude && isset($colors[$exclude])) {
            unset($colors[$exclude]);
        }

        return $colors;
    }
}
