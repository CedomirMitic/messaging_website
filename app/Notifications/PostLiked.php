<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PostLiked extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $post;
    public $liker;

    public function __construct(Post $post, User $liker)
    {
        $this->post = $post;
        $this->liker = $liker;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for the database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->liker->id,
            'user_name' => $this->liker->name,
            'post_title' => $this->post->title,
            'post_id' => $this->post->id,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->post->user_id),
        ];
    }

    /**
     * Definšemo jasno ime eventa da ga lako uhvatimo na frontendu
     */
    public function broadcastAs(): string
    {
        return 'PostLikedEvent';
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'user_id' => $this->liker->id,
            'user_name' => $this->liker->name,
            'post_title' => $this->post->title,
            'post_id' => $this->post->id,
        ]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}