<?php

namespace App\Support;

class UuidHelper
{
    public static function generate(): string
    {
        $timestamp = now()->valueOf();
        $firstSegment = str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        $secondSegment = str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);

        return "{$timestamp}-{$firstSegment}-{$secondSegment}";
    }
}


