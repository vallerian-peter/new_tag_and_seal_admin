<?php

namespace App\Filament\Resources\Helpers;

use App\Models\Livestock;
use App\Support\UuidHelper;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class EventLogFormHelpers
{
    /**
     * Generate UUID field with timestamp-random-eventType format
     * Kept short to fit in database uuid columns (max 36 chars)
     */
    public static function uuidField(string $eventType): TextInput
    {
        return TextInput::make('uuid')
            ->label('UUID')
            ->default(function () use ($eventType) {
                // Format: timestamp-randomNumber-eventType
                $timestamp = now()->timestamp;
                $random = mt_rand(100000, 999999);
                return "{$timestamp}-{$random}-{$eventType}";
            })
            ->readOnly()
            ->required()
            ->maxLength(255)
            ->helperText('Auto-generated: timestamp-random-eventType');
    }

    /**
     * Generate Farm select field with live updates
     */
    public static function farmField(): Select
    {
        return Select::make('farmUuid')
            ->label('Farm')
            ->relationship('farm', 'name')
            ->getOptionLabelFromRecordUsing(fn ($record) => trim(
                ($record->name ?? $record->uuid) . ' (' . ($record->referenceNo ?? 'N/A') . ')'
            ))
            ->searchable()
            ->preload()
            ->required()
            ->live()
            ->afterStateUpdated(function ($state, callable $set) {
                // Clear livestock selection when farm changes
                $set('livestockUuid', null);
            });
    }

    /**
     * Generate Livestock select field with dynamic options based on farm
     */
    public static function livestockField(): Select
    {
        return Select::make('livestockUuid')
            ->label('Livestock')
            ->options(function (Get $get) {
                $farmUuid = $get('farmUuid');
                if (!$farmUuid) {
                    return [];
                }

                // Only show livestock from the selected farm
                return Livestock::where('farmUuid', $farmUuid)
                    ->get()
                    ->mapWithKeys(function ($livestock) {
                        $label = trim(
                            ($livestock->identificationNumber ?? $livestock->uuid) .
                            ($livestock->name ? " - {$livestock->name}" : '') .
                            ($livestock->species ? " ({$livestock->species->name})" : '')
                        );
                        return [$livestock->uuid => $label];
                    })
                    ->toArray();
            })
            ->searchable()
            ->required()
            ->disabled(fn (Get $get) => !$get('farmUuid'))
            ->helperText(fn (Get $get) => !$get('farmUuid') ? 'Please select a farm first' : null);
    }

    /**
     * Generate Event Date DateTimePicker field
     * Allows users to enter the actual date an event occurred
     */
    public static function eventDateField(): DateTimePicker
    {
        return DateTimePicker::make('eventDate')
            ->label('Event Date')
            ->helperText('Date and time when the event actually occurred')
            ->displayFormat('d M Y, H:i')
            ->seconds(false)
            ->afterStateHydrated(function (DateTimePicker $component, $state): void {
                if (blank($state) || $state instanceof Carbon) {
                    return;
                }

                try {
                    $component->state(Carbon::parse($state));
                } catch (\Throwable) {
                    $component->state(null);
                }
            })
            ->dehydrateStateUsing(function ($state) {
                if (blank($state)) {
                    return null;
                }

                if ($state instanceof Carbon) {
                    return $state->format('Y-m-d H:i:s');
                }

                return (string) $state;
            })
            ->nullable();
    }
}

