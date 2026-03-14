<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Post;
use App\Models\User;

class PostLiked extends Notification
{
    use Queueable;

    public $post;
    public $liker;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, User $liker)
    {
        $this->post = $post;
        $this->liker = $liker;
    }

    /**
     * Get the notification's delivery channels.
     * This is the method that was missing!
     */
    public function via($notifiable)
    {
        return ['database'];
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
     * Optional: This also handles the 'database' type if toDatabase is missing
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}