<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmUser extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'farm_users';

    /**
     * The attributes that are mass assignable.
     *
     * NOTE: This is intentionally kept in sync with the
     * backend `FarmUser` model so both apps operate on the
     * same table consistently.
     */
    protected $fillable = [
        'uuid',
        'farmUuid',
        'firstName',
        'middleName',
        'lastName',
        'phone',
        'email',
        'roleTitle',
        'gender',
        'updated_at',
    ];

    /**
     * Farm relationship.
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farmUuid', 'uuid');
    }
}


