<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

/** Guided controls edit the canonical rule rather than a second rule store. */
class EligibilityEditor
{
    public static function selected(mixed $rule, string $fact): array
    {
        if (is_string($rule)) {
            $rule = json_decode($rule, true);
        }
        $clauses = $rule['all'] ?? ($rule ? [$rule] : []);
        foreach ($clauses as $clause) {
            if (($clause['fact'] ?? null) === $fact && ($clause['operator'] ?? null) === 'in') {
                return $clause['value'] ?? [];
            }
        }

        return [];
    }

    public static function update(mixed $rule, string $fact, array $values): ?array
    {
        if (is_string($rule) && trim($rule) !== '') {
            try {
                $rule = json_decode($rule, true, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw ValidationException::withMessages(['data.eligibility_rule_json' => 'Correct the advanced rule JSON before changing applicability.']);
            }
        }
        $clauses = $rule['all'] ?? ($rule ? [$rule] : []);
        $clauses = array_values(array_filter($clauses, fn ($clause) => ($clause['fact'] ?? null) !== $fact || ($clause['operator'] ?? null) !== 'in'));
        if ($values !== []) {
            $clauses[] = ['fact' => $fact, 'operator' => 'in', 'value' => array_values($values)];
        }

        return $clauses === [] ? null : ['all' => $clauses];
    }
}
