<?php

namespace App\Services;

use App\Models\AnimalSubject;
use App\Models\AuditLog;
use App\Models\SyncTombstone;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubjectLifecycleService
{
    public function archive(AnimalSubject $subject, User $actor, string $reason): void
    {
        DB::transaction(function () use ($subject, $actor, $reason): void {
            $before = $subject->attributesToArray();
            $subject->forceFill([
                'deleted_by_uuid' => $actor->uuid,
                'delete_reason' => $reason,
                'restored_at' => null,
                'restored_by_uuid' => null,
                'revision' => ((int) $subject->revision) + 1,
            ])->save();
            $subject->delete();
            $tombstone = SyncTombstone::firstOrNew([
                'entity_type' => 'animal_subject',
                'entity_uuid' => $subject->uuid,
            ]);
            $tombstone->uuid ??= (string) Str::uuid();
            $tombstone->fill([
                'farm_uuid' => $subject->farm_uuid,
                'entity_revision' => $subject->revision,
                'operation' => 'deleted',
                'occurred_at' => now(),
                'superseded_at' => null,
            ])->save();
            $this->audit('animal_subject.deleted', $subject, $actor, $reason, $before);
            $this->queueSyncChange($subject, 'delete');
        });
    }

    public function restore(AnimalSubject $subject, User $actor): void
    {
        DB::transaction(function () use ($subject, $actor): void {
            $before = $subject->attributesToArray();
            $subject->forceFill([
                'restored_at' => now(),
                'restored_by_uuid' => $actor->uuid,
                'revision' => ((int) $subject->revision) + 1,
            ]);
            $subject->restore();
            SyncTombstone::query()->where('entity_type', 'animal_subject')->where('entity_uuid', $subject->uuid)->whereNull('superseded_at')->update(['superseded_at' => now()]);
            $this->audit('animal_subject.restored', $subject, $actor, null, $before);
            $this->queueSyncChange($subject, 'upsert');
        });
    }

    /** The backend writes the device change feed; it records this before serving the feed. */
    private function queueSyncChange(AnimalSubject $subject, string $action): void
    {
        DB::table('sync_change_requests')->insert(['entity_type' => 'animal_subject', 'entity_uuid' => $subject->uuid,
            'action' => $action, 'revision' => $subject->revision, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function audit(string $action, AnimalSubject $subject, User $actor, ?string $reason, array $before): void
    {
        AuditLog::create([
            'uuid' => (string) Str::uuid(),
            'actor_uuid' => $actor->uuid,
            'action' => $action,
            'entity_type' => 'animal_subject',
            'entity_uuid' => $subject->uuid,
            'before_json' => $before,
            'after_json' => $subject->attributesToArray(),
            'reason' => $reason,
            'request_uuid' => (string) Str::uuid(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
