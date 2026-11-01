<?php

namespace App\Filament\Actions;

use App\Models\EventRecord;
use App\Services\DynamicEventSchemaService;
use App\Services\EventLifecycleService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class EditPayloadAction
{
    public static function make(): Action
    {
        return Action::make('edit_payload')
            ->label('Edit Payload')
            ->icon('heroicon-o-pencil-square')
            ->color('warning')
            ->visible(fn () => auth()->user()?->hasPlatformPermission('records.edit') ?? auth()->user()?->hasPlatformPermission('configuration.manage') ?? true)
            ->fillForm(function (EventRecord $record): array {
                return [
                    'payload' => $record->payload_json ?? [],
                    'occurred_at' => $record->occurred_at?->format('Y-m-d H:i:s'),
                    'reason' => '',
                ];
            })
            ->form(function (EventRecord $record): array {
                $schemaService = app(DynamicEventSchemaService::class);
                $configuredFields = $schemaService->getFieldsForEvent($record->event_code);
                $payload = $record->payload_json ?? [];

                $formFields = [];

                // Render configured form fields first with labels
                foreach ($payload as $key => $value) {
                    $meta = $configuredFields[$key] ?? null;
                    $label = $meta['label'] ?? Str::headline((string) $key);
                    $component = $meta['component'] ?? (is_numeric($value) ? 'number' : (is_bool($value) ? 'boolean' : 'text'));

                    if ($component === 'number' || is_numeric($value)) {
                        $input = TextInput::make("payload.{$key}")
                            ->label($label)
                            ->numeric();
                        if (! empty($meta['unit'])) {
                            $input->suffix($meta['unit']);
                        }
                        $formFields[] = $input;
                    } elseif ($component === 'boolean' || is_bool($value)) {
                        $formFields[] = Select::make("payload.{$key}")
                            ->label($label)
                            ->options([1 => 'Yes', 0 => 'No']);
                    } elseif (is_array($value)) {
                        $formFields[] = Textarea::make("payload.{$key}")
                            ->label($label)
                            ->rows(3)
                            ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                            ->dehydrateStateUsing(fn ($state) => is_string($state) ? json_decode($state, true) : $state);
                    } else {
                        $formFields[] = TextInput::make("payload.{$key}")
                            ->label($label);
                    }
                }

                return [
                    Section::make('Payload Attributes')
                        ->description('Modify individual event data points. All modifications are revisioned and audited.')
                        ->schema([
                            Grid::make(2)->schema($formFields),
                        ]),

                    Section::make('Correction Details')
                        ->schema([
                            DateTimePicker::make('occurred_at')
                                ->label('Event Date & Time')
                                ->required(),

                            Textarea::make('reason')
                                ->label('Reason for Correction / Adjustment')
                                ->required()
                                ->minLength(3)
                                ->maxLength(1000)
                                ->placeholder('Please state why this event payload is being updated (e.g. scale calibration correction, clerical error).'),
                        ]),
                ];
            })
            ->action(function (EventRecord $record, array $data): void {
                app(EventLifecycleService::class)->updatePayload(
                    $record,
                    $data['payload'] ?? [],
                    $data['reason'] ?? null,
                    auth()->user()
                );

                if (! empty($data['occurred_at'])) {
                    $record->occurred_at = $data['occurred_at'];
                    $record->save();
                }

                Notification::make()
                    ->title('Event payload updated')
                    ->body("Payload updated to revision #{$record->revision} with audit log entry.")
                    ->success()
                    ->send();
            });
    }
}
