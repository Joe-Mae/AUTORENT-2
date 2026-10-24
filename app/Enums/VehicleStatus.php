<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Available = 'available';
    case Maintenance = 'maintenance';
    case Unavailable = 'unavailable';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::Maintenance => 'En maintenance',
            self::Unavailable => 'Indisponible',
        };
    }
}
