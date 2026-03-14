<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageRead implements ShouldBroadcast
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
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->combined_id),
        ];
    }

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
