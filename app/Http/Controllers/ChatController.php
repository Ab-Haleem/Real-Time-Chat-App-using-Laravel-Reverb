<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(){
        $messages = Message::with('user')->get();
        return view('chat', compact('messages'));
    }

    public function store(Request $request) {

        // Validate the request
        $request->validate(['message' => 'required']);

        // Create the message
        $message = Message::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Broadcast the message to others
        broadcast(new MessageEvent(Auth::user(), $message->load('user')))->toOthers();

        // Return a JSON response
        return response()->json(['success' => true]);
    }
}
