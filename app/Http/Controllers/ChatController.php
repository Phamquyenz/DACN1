<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    public function getMessages()
    {
        $sessionId = session()->getId();
        
        $messages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Đánh dấu tin nhắn của Admin đã đọc khi khách hàng xem
        ChatMessage::where('session_id', $sessionId)
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = ChatMessage::create([
            'session_id' => session()->getId(),
            'user_id' => auth()->id(), // null nếu chưa đăng nhập
            'sender_type' => 'customer',
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }
}
