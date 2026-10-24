<?php

namespace App\Enums;

enum ReservationType: string
{
    case Vehicle = 'vehicle';
    case VehicleWithDriver = 'vehicle_with_driver';
    case DriverOnly = 'driver_only';

    public function label(): string
    {
        return match ($this) {
            self::Vehicle => 'Location de voiture',
            self::VehicleWithDriver => 'Voiture avec chauffeur',
            self::DriverOnly => 'Chauffeur seul',
        };
    }

    public function requiresVehicle(): bool
    {
        return $this !== self::DriverOnly;
    }

    public function requiresDriver(): bool
    {
        return $this !== self::Vehicle;
    }
}
