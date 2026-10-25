<?php

namespace App\Console\Commands;

use App\Enums\ReservationStatus;
use App\Models\ActivityLog;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Console\Command;

/**
 * Purge RGPD des pièces d'identité (spécification §8) : supprime les
 * documents des réservations terminées au-delà de la durée de
 * conservation configurée. Planifiée chaque nuit.
 */
class PurgeIdentityDocuments extends Command
{
    protected $signature = 'reservations:purge-identity-documents';

    protected $description = 'Supprime les pièces d\'identité au-delà de la durée de conservation (RGPD)';

    public function handle(ReservationService $reservations): int
    {
        $retentionMonths = max(1, (int) setting('identity_retention_months', '24'));
        $cutoff = now()->subMonths($retentionMonths);

        $expired = Reservation::query()
            ->whereIn('status', [
                ReservationStatus::Completed,
                ReservationStatus::Cancelled,
                ReservationStatus::Rejected,
            ])
            ->where('created_at', '<', $cutoff)
            ->whereHas('documents')
            ->get();

        foreach ($expired as $reservation) {
            $reservations->purgeIdentityDocuments($reservation);

            ActivityLog::create([
                'user_id' => null,
                'action' => 'identity_documents.purged',
                'description' => $reservation->code,
                'properties' => ['reason' => 'retention_delay_expired'],
            ]);
        }

        $this->info("{$expired->count()} réservation(s) purgée(s) de leurs pièces d'identité.");

        return self::SUCCESS;
    }
}
