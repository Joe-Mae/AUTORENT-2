<?php

namespace App\Services;

use App\Enums\ReservationStatus;
use App\Models\Vehicle;
use Carbon\CarbonPeriod;
use DateTimeInterface;
use Illuminate\Support\Carbon;

/**
 * Contrôle de disponibilité d'un véhicule sur une période.
 *
 * Règle métier (spécification §5.4) : seules les réservations confirmées
 * ou en cours bloquent un véhicule, ainsi que les périodes de maintenance.
 * Cette règle reste modifiable côté serveur en un seul endroit.
 */
class AvailabilityService
{
    public function isAvailable(Vehicle $vehicle, DateTimeInterface $startsAt, DateTimeInterface $endsAt): bool
    {
        if ($vehicle->status?->value !== 'available') {
            return false;
        }

        if ($this->hasBlockingReservation($vehicle, $startsAt, $endsAt)) {
            return false;
        }

        return ! $vehicle->blockedPeriods()
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt)
            ->exists();
    }

    /**
     * Jours indisponibles (format Y-m-d) entre deux dates, pour
     * l'affichage du calendrier de la fiche véhicule.
     *
     * @return array<int, string>
     */
    public function unavailableDates(Vehicle $vehicle, DateTimeInterface $from, DateTimeInterface $to): array
    {
        $dates = [];

        $reservations = $vehicle->reservations()
            ->whereIn('status', [ReservationStatus::Confirmed, ReservationStatus::Ongoing])
            ->where('starts_at', '<', $to)
            ->where('ends_at', '>', $from)
            ->get();

        foreach ($reservations as $reservation) {
            $this->addDays($dates, $reservation->starts_at, $reservation->ends_at);
        }

        $blockedPeriods = $vehicle->blockedPeriods()
            ->where('starts_at', '<', $to)
            ->where('ends_at', '>', $from)
            ->get();

        foreach ($blockedPeriods as $period) {
            $this->addDays($dates, $period->starts_at, $period->ends_at);
        }

        return array_values(array_unique($dates));
    }

    private function hasBlockingReservation(Vehicle $vehicle, DateTimeInterface $startsAt, DateTimeInterface $endsAt): bool
    {
        return $vehicle->reservations()
            ->blocking()
            ->overlapping($startsAt, $endsAt)
            ->exists();
    }

    /**
     * @param  array<int, string>  $dates
     */
    private function addDays(array &$dates, DateTimeInterface $startsAt, DateTimeInterface $endsAt): void
    {
        $period = CarbonPeriod::create(
            Carbon::parse($startsAt)->startOfDay(),
            Carbon::parse($endsAt)->subSecond()->startOfDay(),
        );

        foreach ($period as $day) {
            $dates[] = $day->format('Y-m-d');
        }
    }
}
