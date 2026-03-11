<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Cache;

class AdminMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(20);

        // Mark all as read when viewing list
        ContactMessage::unread()->update(['is_read' => true]);
        Cache::forget('unread_messages_count');

        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        if (!$message->is_read) {
            $message->update(['is_read' => true]);
            Cache::forget('unread_messages_count');
        }
        return view('admin.messages.show', compact('message'));
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        Cache::forget('unread_messages_count');
        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
