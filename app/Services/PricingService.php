<?php

namespace App\Services;

use App\Enums\ReservationType;
use App\Models\Vehicle;
use Carbon\CarbonInterface;

/**
 * Toute la logique de tarification du site (spécification §11).
 *
 * Les tarifs et seuils viennent des settings : le Super Admin peut les
 * modifier sans toucher au code. Le prix affiché côté client en JS n'est
 * qu'indicatif ; ce service est la seule source de vérité.
 */
class PricingService
{
    /**
     * Nombre de jours facturés entre deux dates.
     *
     * « started_periods » (défaut) : chaque tranche de 24 h entamée compte
     * pour un jour. « calendar » : chaque jour calendaire entamé compte.
     */
    public function daysCount(CarbonInterface $startsAt, CarbonInterface $endsAt): int
    {
        if ($endsAt->lessThanOrEqualTo($startsAt)) {
            return 0;
        }

        if (setting('day_count_mode') === 'calendar') {
            return (int) $startsAt->copy()->startOfDay()->diffInDays($endsAt->copy()->startOfDay()) + 1;
        }

        return (int) ceil($startsAt->diffInSeconds($endsAt) / 86_400);
    }

    /**
     * Devis complet pour une réservation.
     *
     * @param  ReservationType  $type  Type de réservation
     * @param  Vehicle|null  $vehicle  Obligatoire sauf pour « chauffeur seul »
     * @return array{days: int, vehicle_daily_price: float, driver_daily_price: float, total_price: float}
     */
    public function quote(ReservationType $type, ?Vehicle $vehicle, CarbonInterface $startsAt, CarbonInterface $endsAt): array
    {
        $days = $this->daysCount($startsAt, $endsAt);
        $vehicleDaily = 0.0;
        $driverDaily = 0.0;

        switch ($type) {
            case ReservationType::Vehicle:
                $vehicleDaily = (float) $vehicle->daily_price;
                break;

            case ReservationType::VehicleWithDriver:
                // Mode « supplement » (défaut) : le chauffeur s'ajoute au prix
                // du véhicule. Mode « package » : le tarif chauffeur couvre tout.
                if (setting('driver_with_car_mode') !== 'package') {
                    $vehicleDaily = (float) $vehicle->daily_price;
                }
                $driverDaily = (float) setting('driver_with_car_price', '7000');
                break;

            case ReservationType::DriverOnly:
                // Tarif réduit appliqué à TOUS les jours dès que la durée
                // dépasse strictement le seuil (spécification §13).
                $threshold = (int) setting('driver_only_threshold_days', '3');
                $driverDaily = $days > $threshold
                    ? (float) setting('driver_only_price_reduced', '13000')
                    : (float) setting('driver_only_price', '15000');
                break;
        }

        return [
            'days' => $days,
            'vehicle_daily_price' => $vehicleDaily,
            'driver_daily_price' => $driverDaily,
            'total_price' => ($vehicleDaily + $driverDaily) * $days,
        ];
    }
}
