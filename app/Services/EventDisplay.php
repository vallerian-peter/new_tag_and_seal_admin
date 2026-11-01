<?php

namespace App\Services;

use App\Models\AnimalSubject;
use App\Models\EventRecord;
use App\Models\FormDefinition;
use App\Models\LookupSet;
use App\Services\Preview\AnimalFlowPreview;
use Carbon\Carbon;

/**
 * Turns a stored payload into labelled rows using the exact form version it was
 * submitted with. Formatting must match the Flutter DynamicValueFormat.
 */
class EventDisplay
{
    private const UNIT_SYMBOLS = ['kg' => 'kg', 'g' => 'g', 'l' => 'L', 'ml' => 'mL', 'cm' => 'cm', 'm' => 'm', 'celsius' => '°C', 'percent' => '%', 'count' => ''];

    /** Identity, audit and upgrade bookkeeping copied into upgraded legacy payloads. */
    private const SYSTEM_KEY = '/^(id|uuid|_\w+|legacy_?\w+|\w+_uuid|\w+Uuid|(created|updated|deleted)_?at|(created|updated)At|synced|sync_?action)$/i';

    /** @var array<string, ?FormDefinition> */
    private array $forms = [];

    public function __construct(private readonly AnimalFlowPreview $text) {}

    /** @return array<int, array{label: string, value: string}> */
    public function rows(EventRecord $record, string $locale = 'en'): array
    {
        $payload = $record->payload_json ?? [];
        $form = $this->forms[$record->form_uuid.'@'.$record->form_version] ??= FormDefinition::where('uuid', $record->form_uuid)->where('version', $record->form_version)->first();
        $rows = [];
        $shown = [];
        foreach ($form?->schema_json['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                $key = $field['key'] ?? null;
                if (! is_string($key) || ! array_key_exists($key, $payload) || $this->blank($payload[$key])) {
                    continue;
                }
                $shown[$key] = true;
                $rows[] = [
                    'label' => $this->text->text($field['presentation']['label'] ?? $field['label'] ?? null, $locale, str($key)->headline()->toString()),
                    'value' => $this->format($field, $payload[$key], $locale),
                ];
            }
        }
        foreach ($payload as $key => $value) {
            if (! isset($shown[$key]) && ! $this->blank($value) && ! preg_match(self::SYSTEM_KEY, (string) $key)) {
                $rows[] = ['label' => str((string) $key)->headline()->toString(), 'value' => $this->format([], $value, $locale)];
            }
        }

        return $rows;
    }

    public function format(array $field, mixed $value, string $locale = 'en'): string
    {
        $component = $field['component'] ?? null;

        return match (true) {
            is_bool($value) => $value ? 'Yes' : 'No',
            $component === 'location' && is_array($value) => sprintf('%.6f, %.6f', $value['latitude'] ?? 0, $value['longitude'] ?? 0),
            $component === 'measurement' && is_numeric($value) => trim($this->number($value).' '.(self::UNIT_SYMBOLS[$field['unit'] ?? ''] ?? '')),
            in_array($component, ['single_select', 'multi_select'], true) => implode(', ', array_map(fn ($item) => $this->option($field, $item, $locale), (array) $value)),
            $component === 'subject_reference' => implode(', ', array_map(fn ($uuid) => AnimalSubject::withTrashed()->where('uuid', $uuid)->value('display_name') ?? $uuid, (array) $value)),
            $component === 'repeatable_group' && is_array($value) => $this->items($field, $value, $locale),
            $component === 'photo' => 'Photo attached',
            $component === 'date' && is_string($value) => $this->date($value, 'j M Y'),
            $component === 'date_time' && is_string($value) => $this->date($value, 'j M Y H:i'),
            is_array($value) => array_is_list($value) ? implode(', ', array_map(fn ($item) => is_scalar($item) ? (string) $item : json_encode($item), $value)) : json_encode($value, JSON_UNESCAPED_UNICODE),
            is_numeric($value) => $this->number($value),
            default => (string) $value,
        };
    }

    private function items(array $field, array $items, string $locale): string
    {
        $subFields = collect($field['item_fields'] ?? [])->keyBy('key');

        return collect($items)->values()->map(function ($item, int $index) use ($subFields, $locale) {
            $parts = collect(is_array($item) ? $item : [])->reject(fn ($value) => $this->blank($value))
                ->map(fn ($value, $key) => $this->text->text($subFields[$key]['label'] ?? null, $locale, str((string) $key)->headline()->toString()).': '.$this->format($subFields[$key] ?? [], $value, $locale));

            return ($index + 1).'. '.$parts->implode(', ');
        })->implode('; ');
    }

    private function option(array $field, mixed $value, string $locale): string
    {
        foreach ($field['options'] ?? [] as $option) {
            if (($option['value'] ?? null) === $value) {
                return $this->text->text($option['label'] ?? null, $locale, (string) $value);
            }
        }
        if (! empty($field['lookup_code'])) {
            $option = LookupSet::where('code', $field['lookup_code'])->orderByDesc('version')->first()?->options()->where('code', $value)->first();
            if ($option) {
                return $this->text->text(array_replace(['en' => $option->label], $option->translations_json ?? []), $locale, (string) $value);
            }
        }

        return (string) $value;
    }

    private function date(string $value, string $format): string
    {
        try {
            return Carbon::parse($value)->format($format);
        } catch (\Throwable) {
            return $value;
        }
    }

    private function number(mixed $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    }

    private function blank(mixed $value): bool
    {
        return $value === null || $value === '' || $value === [];
    }
}
