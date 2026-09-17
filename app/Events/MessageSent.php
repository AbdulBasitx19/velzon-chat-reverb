<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message; //Public isliye hai taake Laravel isay automatically serialize kar sake
    /**
     * Create a new event instance.
     */
    public function __construct(Message $message)
    {
        // Jab event fire hota hai, toh Message object is constructor mein aata hai
        $this->message = $message;
    }



    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        // Yeh batata hai ke event kis channel par broadcast hogi
        // private-chat.{receiver_id} = User ke ID ke sath private channel

        //  CORRECTED: Common channel for both sender and receiver
        // Format: chat.{min(id)}.{max(id)}
        $ids = [$this->message->sender_id, $this->message->receiver_id];
        sort($ids); // Sort karke min aur max nikal liye
        
        return [
            new PrivateChannel('chat.' . $ids[0] . '.' . $ids[1]),
        ];
    }

    public function broadcastAs(): string
    {
        // Yeh event ka naam hai jo frontend (Pusher.js) use karega
        return 'message.sent';
    }

    public function broadcastWith(): array {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'message' => $this->message->message,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}
