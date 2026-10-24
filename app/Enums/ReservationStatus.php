<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case Pending = 'pending';
    case IdentityVerified = 'identity_verified';
    case Confirmed = 'confirmed';
    case Ongoing = 'ongoing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::IdentityVerified => 'Identité vérifiée',
            self::Confirmed => 'Confirmée',
            self::Ongoing => 'En cours',
            self::Completed => 'Terminée',
            self::Cancelled => 'Annulée',
            self::Rejected => 'Refusée',
        };
    }

    /**
     * Statuts qui bloquent la disponibilité d'un véhicule ou d'un chauffeur.
     * Une réservation en attente ne bloque pas (règle configurable).
     */
    public function blocksAvailability(): bool
    {
        return in_array($this, [self::Confirmed, self::Ongoing], true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled, self::Rejected], true);
    }
}
