<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

final class FormLocalization
{
    /** All editable display-text locations; stable field keys are never translated. */
    public function entries(array $schema): array
    {
        $entries = [];
        foreach (['title', 'description', 'submitLabel'] as $property) {
            if (array_key_exists($property, $schema['presentation'] ?? [])) {
                $entries['presentation.'.$property] = [$schema['presentation'][$property], $property === 'title'];
            }
        }
        foreach ($schema['sections'] ?? [] as $s => $section) {
            $entries["sections.$s.label"] = [$section['label'] ?? null, true];
            $entries["sections.$s.description"] = [$section['description'] ?? null, false];
            foreach ($section['fields'] ?? [] as $f => $field) {
                $prefix = "sections.$s.fields.$f";
                $entries[$prefix.'.label'] = [$field['label'] ?? null, true];
                foreach (['description', 'help_text', 'placeholder'] as $property) {
                    $entries[$prefix.'.'.$property] = [$field[$property] ?? null, false];
                }
                foreach ($field['options'] ?? [] as $o => $option) {
                    $entries[$prefix.".options.$o.label"] = [$option['label'] ?? null, true];
                }
                foreach ($field['validation_messages'] ?? [] as $code => $message) {
                    $entries[$prefix.'.validation_messages.'.$code] = [$message, false];
                }
            }
        }

        return $entries;
    }

    public function assertPublishable(array $schema): void
    {
        $errors = [];
        foreach ($this->entries($schema) as $path => [$value, $required]) {
            $en = is_string($value) ? trim($value) : trim((string) ($value['en'] ?? ''));
            $sw = is_array($value) ? trim((string) ($value['sw'] ?? '')) : '';
            if (($required || $sw !== '') && $en === '') {
                $errors['data.schema_json.'.$path.'.en'] = 'Enter the English fallback before publishing.';
            }
        }
        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    public function summary(array $schema): string
    {
        $total = $translated = 0;
        foreach ($this->entries($schema) as [$value, $required]) {
            $en = is_string($value) ? trim($value) : trim((string) ($value['en'] ?? ''));
            if (! $required && $en === '') {
                continue;
            }
            $total++;
            if (is_array($value) && trim((string) ($value['sw'] ?? '')) !== '') {
                $translated++;
            }
        }

        return "$translated / $total Kiswahili";
    }
}
