<?php

namespace App\Models;

use App\Models\LivestockType;
use Illuminate\Database\Eloquent\Model;

class Breed extends Model
{
    protected $fillable = [
        'name',
        'group',
        'livestockTypeId'
    ];

    public function livestockType()
    {
        return $this->belongsTo(LivestockType::class, 'livestockTypeId', 'id');
    }
}
