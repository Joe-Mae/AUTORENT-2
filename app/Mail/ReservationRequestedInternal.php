<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Notification interne à l'agence pour chaque nouvelle demande.
 */
class ReservationRequestedInternal extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Reservation $reservation) {}

    public function build(): static
    {
        return $this->subject('[Agence] '.$this->reservation->code.' · '.$this->reservation->clientFullName())
            ->view('emails.reservation-requested-internal');
    }
}
