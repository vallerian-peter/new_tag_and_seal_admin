<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ExtensionOfficerFarmInvite extends Model
{
    protected $fillable = [
        'extensionOfficerId',
        'access_code',
        'farmerId',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Generate access code before creating
        static::creating(function ($invite) {
            if (empty($invite->access_code)) {
                $invite->access_code = self::generateAccessCode();
            }
        });
    }

    /**
     * Generate access code in format: ACODE-random_5_number_3_characters=7-random_4_numbers
     * Example: ACODE-12345ABC=7-1234
     */
    public static function generateAccessCode(): string
    {
        // Generate random 5 numbers
        $randomNumbers = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        
        // Generate random 3 uppercase characters
        $randomChars = strtoupper(Str::random(3));
        
        // Generate random 4 numbers
        $randomEndNumbers = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        
        return "ACODE-{$randomNumbers}{$randomChars}=7-{$randomEndNumbers}";
    }

    /**
     * Generate a unique access code that doesn't already exist in the database
     */
    public static function generateUniqueAccessCode(): string
    {
        $maxAttempts = 100;
        $attempts = 0;
        
        do {
            $code = self::generateAccessCode();
            $exists = self::where('access_code', $code)->exists();
            $attempts++;
        } while ($exists && $attempts < $maxAttempts);
        
        if ($exists) {
            // Fallback: add timestamp to ensure uniqueness
            $code = self::generateAccessCode() . '-' . time();
        }
        
        return $code;
    }

    public function extensionOfficer(): BelongsTo
    {
        return $this->belongsTo(ExtensionOfficer::class, 'extensionOfficerId');
    }

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class, 'farmerId');
    }

    /**
     * Get the farm associated with this invite through the farmer
     */
    public function farm()
    {
        return $this->farmer()->first()?->farms()->first();
    }
}

