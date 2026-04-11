<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;

class StageChange extends Model
{
    protected $table = 'stage_changes';

    protected $fillable = [
        'uuid',
        'eventDate',
        'farmUuid',
        'livestockUuid',
        'fromStageId',
        'toStageId',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $record): void {
            if (empty($record->uuid)) {
                $record->uuid = UuidHelper::generate();
            }
        });
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }

    public function livestock()
    {
        return $this->belongsTo(Livestock::class, 'livestockUuid', 'uuid');
    }

    public function fromStage()
    {
        return $this->belongsTo(Stage::class, 'fromStageId');
    }

    public function toStage()
    {
        return $this->belongsTo(Stage::class, 'toStageId');
    }
}

