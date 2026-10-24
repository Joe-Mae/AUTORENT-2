<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email envoyé au client à la création de sa demande : code de suivi,
 * récapitulatif et rappel du permis à présenter à l'agence (§5.1).
 */
class ReservationRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Reservation $reservation) {}

    public function build(): static
    {
        return $this->subject(__('site.booking.mail.subject', ['code' => $this->reservation->code]))
            ->view('emails.reservation-requested');
    }
}
