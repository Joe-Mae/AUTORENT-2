<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly ContactMessage $contactMessage)
    {
    }

    public function build(): static
    {
        return $this->subject(__('Nouveau message de contact').' · '.$this->contactMessage->name)
            ->view('emails.contact');
    }
}
