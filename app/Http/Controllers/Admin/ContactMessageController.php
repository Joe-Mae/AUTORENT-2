<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        return view('admin.messages.index', [
            'messages' => ContactMessage::latest()->paginate(15),
        ]);
    }

    public function show(ContactMessage $message)
    {
        $message->update(['is_read' => true]);

        return view('admin.messages.show', ['message' => $message]);
    }

    public function toggleRead(ContactMessage $message)
    {
        $message->update([
            'is_read' => ! $message->is_read,
            'treated_at' => $message->is_read ? null : now(),
        ]);

        return back();
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('status', __('site.admin.messages.deleted'));
    }
}
