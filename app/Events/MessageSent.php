<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $combined_id;

    public function __construct(public ChatMessage $message, string $combined_id)
    {
        $this->message = $message;
        $this->combined_id = $combined_id;
    }

    /**
     * Get the channels the event should broadcast on.
     */
public function broadcastOn(): array
{
    return [
        // Chat-specific channel for the current conversation
        new PrivateChannel('chat.' . $this->combined_id),

        // General notification channel for the receiver
        new PrivateChannel('chat.' . $this->message->receiver_id),
    ];
}


    /**
     * The data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            "id" => $this->message->id,
            "sender_id" => $this->message->sender_id,
            "receiver_id" => $this->message->receiver_id,
            "message" => $this->message->message,
            "read_at" => $this->message->read_at,
        ];
    }
}

