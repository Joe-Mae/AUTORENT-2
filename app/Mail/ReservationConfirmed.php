<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Reservation $reservation) {}

    public function build(): static
    {
        return $this->subject(__('site.admin.reservations.mail.confirmed_subject', ['code' => $this->reservation->code]))
            ->view('emails.reservation-confirmed');
    }
}
