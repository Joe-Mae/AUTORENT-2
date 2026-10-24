<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationRejected extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Reservation $reservation,
        public readonly ?string $reason = null,
    ) {}

    public function build(): static
    {
        return $this->subject(__('site.admin.reservations.mail.rejected_subject', ['code' => $this->reservation->code]))
            ->view('emails.reservation-rejected');
    }
}
