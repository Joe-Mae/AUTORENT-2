<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(ContactRequest $request)
    {
        $message = ContactMessage::create($request->validated());

        // File d'attente : l'envoi ne bloque pas la réponse au client.
        Mail::to(setting('contact_email'))->queue(new ContactMessageReceived($message));

        return back()->with('status', __('site.contact.thanks'));
    }
}
