<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrepuceCondition extends Model
{
    protected $table = 'prepuce_conditions';

    protected $fillable = [
        'uuid',
        'eventDate',
        'farmUuid',
        'livestockUuid',
        'conditionTypeId',
        'severityId',
        'clinicalSignIds',
        'causeRiskId',
        'treatmentGivenIds',
        'medicineId',
        'administrationRouteId',
        'vetId',
        'extensionOfficerId',
        'quantity',
        'dose',
        'breedingStatusId',
        'healingStatusId',
        'followUpDate',
        'notes',
    ];

    protected $casts = [
        'clinicalSignIds' => 'array',
        'treatmentGivenIds' => 'array',
        'followUpDate' => 'datetime',
        'eventDate' => 'datetime',
        'medicineId' => 'integer',
        'administrationRouteId' => 'integer',
        'conditionTypeId' => 'integer',
        'severityId' => 'integer',
        'causeRiskId' => 'integer',
        'breedingStatusId' => 'integer',
        'healingStatusId' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $record): void {
            if (empty($record->uuid)) {
                $record->uuid = UuidHelper::generate();
            }
        });
    }

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }

    public function livestock(): BelongsTo
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicines::class, 'medicineId');
    }

    public function administrationRoute(): BelongsTo
    {
        return $this->belongsTo(AdministrationRoute::class, 'administrationRouteId');
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(Vet::class, 'vetId', 'medicalLicenseNo');
    }

    public function extensionOfficer(): BelongsTo
    {
        return $this->belongsTo(ExtensionOfficer::class, 'extensionOfficerId', 'medicalLicenseNo');
    }

    public function conditionType(): BelongsTo
    {
        return $this->belongsTo(PrepuceConditionType::class, 'conditionTypeId', 'id');
    }

    public function severity(): BelongsTo
    {
        return $this->belongsTo(PrepuceSeverity::class, 'severityId', 'id');
    }

    public function causeRisk(): BelongsTo
    {
        return $this->belongsTo(PrepuceCauseRisk::class, 'causeRiskId', 'id');
    }

    public function breedingStatus(): BelongsTo
    {
        return $this->belongsTo(PrepuceBreedingStatus::class, 'breedingStatusId', 'id');
    }

    public function healingStatus(): BelongsTo
    {
        return $this->belongsTo(PrepuceHealingStatus::class, 'healingStatusId', 'id');
    }
}
