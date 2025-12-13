<?php

namespace App\Filament\Resources\Livestocks\Schemas;

use App\Support\UuidHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('uuid')
                    ->label('UUID')
                    ->default(fn () => UuidHelper::generate())
                    ->readOnly()
                    ->required(),
                TextInput::make('identificationNumber')
                    ->required(),
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
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->identificationNumber ?? $record->uuid) . ' ' . ($record->name ? "- {$record->name}" : '')))
                    ->searchable()
                    ->preload()
                    ->nullable(),
                Select::make('fatherUuid')
                    ->label('Father')
                    ->relationship('father', 'identificationNumber')
                    ->getOptionLabelFromRecordUsing(fn ($record) => trim(($record->identificationNumber ?? $record->uuid) . ' ' . ($record->name ? "- {$record->name}" : '')))
                    ->searchable()
                    ->preload()
                    ->nullable(),
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
                    ->options(self::getColorOptions())
                    ->searchable()
                    ->nullable()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        // If primary color is selected and matches secondary color, clear secondary color
                        $secondaryColor = $get('secondaryColor');
                        if ($state === $secondaryColor) {
                            $set('secondaryColor', null);
                        }
                    }),
                Select::make('secondaryColor')
                    ->label('Secondary Color')
                    ->options(function (callable $get) {
                        $primaryColor = $get('primaryColor');
                        $options = self::getColorOptions();
                        // Filter out primary color from secondary color options
                        if ($primaryColor && isset($options[$primaryColor])) {
                            unset($options[$primaryColor]);
                        }
                        return $options;
                    })
                    ->searchable()
                    ->nullable()
                    ->live(),
            ]);
    }

    /**
     * Get color options for dropdown
     */
    public static function getColorOptions(): array
    {
        return [
            'red' => 'Red',
            'green' => 'Green',
            'blue' => 'Blue',
            'black' => 'Black',
            'white' => 'White',
            'brown' => 'Brown',
            'yellow' => 'Yellow',
            'orange' => 'Orange',
            'pink' => 'Pink',
            'gray' => 'Gray',
            'grey' => 'Grey',
            'purple' => 'Purple',
            'tan' => 'Tan',
            'beige' => 'Beige',
            'cream' => 'Cream',
            'gold' => 'Gold',
            'silver' => 'Silver',
            'mixed' => 'Mixed',
        ];
    }
}
