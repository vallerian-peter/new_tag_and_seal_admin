<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\EventRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventLifecycleService
{
    /**
     * Update payload attributes on an event record in an audited transaction.
     *
     * @param EventRecord $record
     * @param array<string, mixed> $payloadUpdates
     * @param string|null $reason
     * @param User|null $actor
     * @return EventRecord
     */
    public function updatePayload(EventRecord $record, array $payloadUpdates, ?string $reason, ?User $actor = null): EventRecord
    {
        return DB::transaction(function () use ($record, $payloadUpdates, $reason, $actor) {
            $beforePayload = $record->payload_json ?? [];
            $mergedPayload = array_merge($beforePayload, $payloadUpdates);

            $beforeRecord = $record->attributesToArray();

            $record->payload_json = $mergedPayload;
            $record->revision = ((int) $record->revision) + 1;
            $record->updated_at = now();
            $record->save();

            // Re-sync projections if this event is mapped to a behaviour engine rule
            $this->syncProjections($record);

            // Audit the payload modification
            AuditLog::create([
                'uuid' => (string) Str::uuid(),
                'actor_uuid' => $actor?->uuid,
                'action' => 'event_record.payload_updated',
                'entity_type' => 'event_record',
                'entity_uuid' => $record->uuid,
                'before_json' => $beforeRecord,
                'after_json' => $record->fresh()->attributesToArray(),
                'reason' => $reason ?: 'Manual payload correction',
                'request_uuid' => request()->header('X-Request-Id', (string) Str::uuid()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $record;
        });
    }

    /**
     * Recalculate projections affected by this payload modification.
     */
    protected function syncProjections(EventRecord $record): void
    {
        $payload = $record->payload_json ?? [];

        // If weight change, update animal_subjects attributes
        if ($record->behavior_code === 'update_weight' && isset($payload['weight']) && is_numeric($payload['weight'])) {
            $subject = $record->subject;
            if ($subject) {
                $attrs = $subject->attributes_json ?? [];
                $attrs['current_weight'] = (float) $payload['weight'];
                $subject->attributes_json = $attrs;
                $subject->save();
            }
        }

        // If birth event, update subject_reproduction_state totals
        if ($record->behavior_code === 'record_birth' && (isset($payload['born_alive']) || isset($payload['born_dead']))) {
            $state = DB::table('subject_reproduction_state')->where('subject_uuid', $record->subject_uuid)->first();
            if ($state) {
                $bornAlive = isset($payload['born_alive']) ? (int) $payload['born_alive'] : $state->last_born_alive;
                $bornDead = isset($payload['born_dead']) ? (int) $payload['born_dead'] : $state->last_born_dead;
                DB::table('subject_reproduction_state')
                    ->where('subject_uuid', $record->subject_uuid)
                    ->update([
                        'last_born_alive' => $bornAlive,
                        'last_born_dead' => $bornDead,
                        'updated_at' => now(),
                    ]);
            }
        }

        // If production event, update subject_production_daily
        if ($record->behavior_code === 'record_production' && isset($payload['quantity'])) {
            $date = $record->occurred_at ? $record->occurred_at->toDateString() : now()->toDateString();
            DB::table('subject_production_daily')
                ->where('subject_uuid', $record->subject_uuid)
                ->where('production_date', $date)
                ->update([
                    'quantity' => (float) ($payload['quantity'] ?? 0),
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Archive (void) an event record in an audited transaction.
     */
    public function archive(EventRecord $record, ?string $reason, ?User $actor = null): EventRecord
    {
        return DB::transaction(function () use ($record, $reason, $actor) {
            $beforeRecord = $record->attributesToArray();

            $record->record_status = 'voided';
            $record->voided_at = now();
            $record->void_reason = $reason ?: 'Archived by administrator';
            $record->voided_by_uuid = $actor?->uuid;
            $record->revision = ((int) $record->revision) + 1;
            $record->updated_at = now();
            $record->save();

            // Re-sync projections after voiding
            $this->syncProjections($record);

            AuditLog::create([
                'uuid' => (string) Str::uuid(),
                'actor_uuid' => $actor?->uuid,
                'action' => 'event_record.archived',
                'entity_type' => 'event_record',
                'entity_uuid' => $record->uuid,
                'before_json' => $beforeRecord,
                'after_json' => $record->fresh()->attributesToArray(),
                'reason' => $reason ?: 'Event record archived / voided',
                'request_uuid' => request()->header('X-Request-Id', (string) Str::uuid()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $record;
        });
    }

    /**
     * Restore an archived (voided) event record back to confirmed status.
     */
    public function restore(EventRecord $record, ?string $reason, ?User $actor = null): EventRecord
    {
        return DB::transaction(function () use ($record, $reason, $actor) {
            $beforeRecord = $record->attributesToArray();

            $record->record_status = 'confirmed';
            $record->voided_at = null;
            $record->void_reason = null;
            $record->voided_by_uuid = null;
            $record->revision = ((int) $record->revision) + 1;
            $record->updated_at = now();
            $record->save();

            // Re-sync projections after restoring
            $this->syncProjections($record);

            AuditLog::create([
                'uuid' => (string) Str::uuid(),
                'actor_uuid' => $actor?->uuid,
                'action' => 'event_record.restored',
                'entity_type' => 'event_record',
                'entity_uuid' => $record->uuid,
                'before_json' => $beforeRecord,
                'after_json' => $record->fresh()->attributesToArray(),
                'reason' => $reason ?: 'Event record restored from archive',
                'request_uuid' => request()->header('X-Request-Id', (string) Str::uuid()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $record;
        });
    }

    /**
     * Permanently delete an event record in an audited transaction.
     */
    public function forceDelete(EventRecord $record, ?string $reason, ?User $actor = null): bool
    {
        return DB::transaction(function () use ($record, $reason, $actor) {
            $beforeRecord = $record->attributesToArray();

            AuditLog::create([
                'uuid' => (string) Str::uuid(),
                'actor_uuid' => $actor?->uuid,
                'action' => 'event_record.permanently_deleted',
                'entity_type' => 'event_record',
                'entity_uuid' => $record->uuid,
                'before_json' => $beforeRecord,
                'after_json' => null,
                'reason' => $reason ?: 'Permanently deleted event record',
                'request_uuid' => request()->header('X-Request-Id', (string) Str::uuid()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $record->delete();
        });
    }
}
