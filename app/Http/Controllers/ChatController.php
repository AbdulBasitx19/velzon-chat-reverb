<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;

class ChatController extends Controller
{
    //
    public function index()
    {
        $users = User::where('id', '!=', auth()->id())->get();

        return view('chat.index', compact('users'));
    }

    public function store(Request $request){
        $request->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'required|exists:users,id',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request['receiver_id'],
            'message' => $request['message'],
        ]);

        event(new MessageSent($message));

        // ajax k liay response
        return response()->json([
            'success' => 'true',
            'message' => $message->load('sender'), // Sender ki details bhej di
        ]);
    }

    public function getMessages($userId)
    {
        $messages = Message::where(function($query) use ($userId)
        {
            $query->where('sender_id', auth()->id())->where('receiver_id', $userId);
        })->orWhere(function($query) use ($userId){
            $query->where('sender_id', $userId)->where('receiver_id', auth()->id());
        })->with('sender') // Sender ki details bhi load karo
        ->orderBy('created_at', 'asc') // Purane messages pehle
        ->get();

        return response()->json($messages);
    }

}
