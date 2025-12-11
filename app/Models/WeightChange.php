<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\UuidHelper;

class WeightChange extends Model
{
    protected $fillable = [
        'uuid',
        'farmUuid',
        'livestockUuid',
        'oldWeight',
        'newWeight',
        'remarks',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $weightChange): void {
            if (empty($weightChange->uuid)) {
                $weightChange->uuid = UuidHelper::generate();
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


