<?php

namespace App\Enums;

enum TransmissionType: string
{
    case Automatic = 'automatic';
    case Manual = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::Automatic => 'Automatique',
            self::Manual => 'Manuelle',
        };
    }
}
