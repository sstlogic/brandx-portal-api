<?php

namespace App\Booked\Enums;

use Illuminate\Support\Str;

enum ReservationLocation: string
{
    case Escac = 'ESCAC';
    case Coscs = 'CoSCS';

    public static function fromResourceName(string $name): ?ReservationLocation
    {
        if (Str::contains($name, self::Escac->value)) {
            return self::Escac;
        }

        if (Str::contains($name, self::Coscs->value)) {
            return self::Coscs;
        }

        return null;
    }
}
