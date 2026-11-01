<?php

namespace App\Filament\Resources\AnimalSubjects\Pages;

use App\Filament\Resources\AnimalSubjects\AnimalSubjectResource;
use App\Models\AnimalSubject;
use App\Models\EventRecord;
use App\Services\BehaviorMapping;
use App\Services\EventDisplay;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class ViewAnimalSubject extends Page
{
    use InteractsWithRecord;

    protected static string $resource = AnimalSubjectResource::class;

    protected string $view = 'filament.animals.view-subject';

    public bool $showVoided = true;

    public function mount(int|string $record): void
    {
        abort_unless(AnimalSubjectResource::canViewAny(), 403);
        $this->record = $this->resolveRecord($record);
    }

    protected function resolveRecord(int|string $key): AnimalSubject
    {
        $query = AnimalSubjectResource::getEloquentQuery()->withoutGlobalScopes([\Illuminate\Database\Eloquent\SoftDeletingScope::class]);
        if (! (auth()->user()?->hasPlatformPermission('records.restore') ?? false)) {
            $query->whereNull('animal_subjects.deleted_at');
        }

        return $query->where('animal_subjects.id', $key)->firstOrFail();
    }

    public function getTitle(): string
    {
        return $this->getRecord()->display_name ?: 'Unnamed animal';
    }

    protected function getViewData(): array
    {
        /** @var AnimalSubject $subject */
        $subject = $this->getRecord();
        $display = app(EventDisplay::class);
        $context = $subject->registration_context_json;
        $context = is_string($context) ? json_decode($context, true) : ($context ?? []);

        $events = EventRecord::query()->with(['definition', 'behavior'])
            ->where('subject_uuid', $subject->uuid)
            ->when(! $this->showVoided, fn ($query) => $query->where('record_status', '!=', 'voided'))
            ->orderByDesc('occurred_at')->orderByDesc('id')->limit(200)->get()
            ->map(fn (EventRecord $event) => [
                'uuid' => $event->uuid,
                'name' => $event->definition?->name ?? str($event->event_code)->headline()->toString(),
                'family' => BehaviorMapping::FAMILIES[$event->familyCode()] ?? null,
                'occurredAt' => $event->occurred_at,
                'voided' => $event->record_status === 'voided',
                'voidReason' => $event->void_reason,
                'rows' => $display->rows($event),
            ]);

        return [
            'subject' => $subject,
            'identifiers' => DB::table('subject_identifiers as i')
                ->leftJoin('identifier_types as t', 't.uuid', '=', 'i.identifier_type_uuid')
                ->where('i.subject_uuid', $subject->uuid)->whereNull('i.deleted_at')
                ->orderBy('i.status')->get(['t.name as type', 'i.display_value', 'i.status']),
            'mother' => $this->relative($subject->attributes_json['mother_uuid'] ?? $context['motherUuid'] ?? null),
            'father' => $this->relative($subject->attributes_json['father_uuid'] ?? $context['fatherUuid'] ?? null),
            'offspring' => AnimalSubject::query()
                ->where(fn ($query) => $query->where('attributes_json->mother_uuid', $subject->uuid)->orWhere('attributes_json->father_uuid', $subject->uuid)
                    ->orWhere('registration_context_json->motherUuid', $subject->uuid)->orWhere('registration_context_json->fatherUuid', $subject->uuid))
                ->orderBy('birth_or_start_date')->get(['id', 'display_name', 'sex_code', 'birth_or_start_date']),
            'reproduction' => DB::table('subject_reproduction_state')->where('subject_uuid', $subject->uuid)->first(),
            'production' => DB::table('subject_production_daily')->where('subject_uuid', $subject->uuid)
                ->where('production_date', '>=', now()->subDays(30)->toDateString())
                ->groupBy('product_code', 'unit')
                ->get(['product_code', 'unit', DB::raw('sum(quantity) as quantity'), DB::raw('sum(damaged_quantity) as damaged'), DB::raw('count(*) as days')]),
            'legacy' => DB::table('legacy_record_mappings')->where('target_uuid', $subject->uuid)->where('target_type', 'animal_subject')->first(['source_table', 'source_key']),
            'events' => $events,
        ];
    }

    private function relative(?string $uuid): ?AnimalSubject
    {
        return $uuid ? AnimalSubject::withTrashed()->where('uuid', $uuid)->first(['id', 'uuid', 'display_name']) : null;
    }
}
