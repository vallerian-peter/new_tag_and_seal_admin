<?php

namespace App\Models;

use App\Support\UuidHelper;
use Illuminate\Database\Eloquent\Model;

class IdentificationEvent extends Model
{
    protected $table = 'identification_events';

    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'identificationNumber',
        'eventDate',
        'remarks',
    ];

    protected $casts = [
        'eventDate' => 'datetime',
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
}

