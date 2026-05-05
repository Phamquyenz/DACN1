<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ChatMessage;
use App\Models\User;

class ContactController extends Controller
{
    public function index()
    {
        // Get all unique sessions with their latest message and unread count
        $sessions = ChatMessage::select('session_id')
            ->selectRaw('MAX(created_at) as last_activity')
            ->selectRaw('SUM(CASE WHEN is_read = 0 AND sender_type = "customer" THEN 1 ELSE 0 END) as unread_count')
            ->selectRaw('MAX(user_id) as user_id') // Try to get user_id if they ever logged in during session
            ->groupBy('session_id')
            ->orderBy('last_activity', 'desc')
            ->get();

        // Attach user info if available
        foreach ($sessions as $session) {
            if ($session->user_id) {
                $session->user = User::find($session->user_id);
            }
        }

        return view('admin.contacts.index', compact('sessions'));
    }

    public function getMessages($sessionId)
    {
        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Mark customer messages as read
        ChatMessage::where('session_id', $sessionId)
            ->where('sender_type', 'customer')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function reply(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = ChatMessage::create([
            'session_id' => $sessionId,
            'user_id' => auth()->id(), // Admin id
            'sender_type' => 'admin',
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }
}
