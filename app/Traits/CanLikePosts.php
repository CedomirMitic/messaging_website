<?php

namespace App\Traits;

use App\Models\Post;
use App\Models\Like;
use App\Notifications\PostLiked;
use Illuminate\Support\Facades\Auth;

trait CanLikePosts
{
    /**
     * Toggle like/unlike on a post.
     */
    public function toggleLike($postId)
    {
        $post = Post::findOrFail($postId);
        $user = Auth::user();
        $userId = $user->id;

        // Provera da li korisnik lajkuje svoj post
        if ($post->user_id === $userId) {
            $this->dispatch('notify', message: 'You cannot like your own post.', type: 'info');
            return;
        }

        $existingLike = Like::where('post_id', $postId)
                            ->where('user_id', $userId)
                            ->first();

        if ($existingLike) {
            // If like exits delete it (Unlike)
            $existingLike->delete();
        } else {
            // If doeesnt exist create it (Like)
            Like::create([
                'post_id' => $postId, 
                'user_id' => $userId
            ]);

            // Send notification to post creator
            $post->user->notify(new PostLiked($post, $user));
        }

        // Automatically refresh component if method exists in component
        if (method_exists($this, 'loadData')) {
            $this->loadData();
        }
    }
}