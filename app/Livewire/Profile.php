<?php

namespace App\Livewire;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Livewire\Component;
use App\Models\FriendList;
use Livewire\WithFileUploads;
use App\Traits\CloudinaryUpload;
use Illuminate\Support\Facades\Auth;
use App\Traits\CanLikePosts;
use App\Traits\ManagesFriends;

class Profile extends Component
{
    use CanLikePosts, ManagesFriends;
    use CloudinaryUpload;
    use WithFileUploads;

    public User $user;
    public bool $isMyProfile = false;
    public bool $isFriend = false;

    // Create properties
    public bool $showPostModal = false;
    public string $title = '';
    public string $content = '';
    public $image;

    // Edit properties
    public $editingPostId = null;
    public $editTitle;
    public $editContent;
    public $editImage;

    public function mount($id = null)
    {
        if (empty($id)) {
            $this->user = auth()->user();
            $this->isMyProfile = true;
        } else {
            $this->user = User::findOrFail($id);
            $this->isMyProfile = (int) $this->user->id === (int) auth()->id();
            $this->isFriend = FriendList::where('user_id', auth()->id())
                ->where('friend_id', $this->user->id)
                ->exists();
        }
    }

    public function submitPost()
    {
        $this->validate([
            'title' => 'required|min:3',
            'content' => 'required',
            'image' => 'nullable|image|max:10240',
        ]);

        $imageUrl = $this->image ? $this->uploadToCloudinary($this->image, 'posts') : null;

        Post::create([
            'user_id' => auth()->id(),
            'title' => $this->title,
            'content' => $this->content,
            'image' => $imageUrl,
        ]);

        $this->reset(['title', 'content', 'image', 'showPostModal']);
        $this->dispatch('notify', message: 'Post created successfully!');
    }

    public function updatePost()
    {
        if (!$this->isMyProfile)
            return;

        $this->validate([
            'editTitle' => 'required|string|max:100',
            'editContent' => 'required|string|max:500',
            'editImage' => 'nullable|image|max:10240',
        ]);

        $post = Post::where('user_id', Auth::id())->findOrFail($this->editingPostId);

        $post->title = $this->editTitle;
        $post->content = $this->editContent;

        if ($this->editImage) {
            // 1. Delete old cloudinary photo if it exists.
            if ($post->image) {
                $this->deleteFromCloudinary($post->image, 'posts');
            }
            $newImageUrl = $this->uploadToCloudinary($this->editImage, 'posts');
            if ($newImageUrl) {
                $post->image = $newImageUrl;
            }
        }

        $post->save();

        $this->reset(['editingPostId', 'editTitle', 'editContent', 'editImage', 'showPostModal']);
        $this->dispatch('notify', message: 'Post updated successfully!');
    }
    public function deletePost($postId)
    {
        if (!$this->isMyProfile)
            return;
        $post = Post::where('user_id', Auth::id())->findOrFail($postId);
        // First delete image from cloudinary
        if ($post->image) {
            $this->deleteFromCloudinary($post->image, 'posts');
        }
        $post->delete();
        $this->dispatch('notify', message: 'Post removed.');
    }

    public function editPost($postId)
    {
        if (!$this->isMyProfile)
            return;
        $post = Post::where('user_id', Auth::id())->findOrFail($postId);

        $this->editingPostId = $postId;
        $this->editTitle = $post->title;
        $this->editContent = $post->content;
        $this->showPostModal = true;
    }

    public function openCreateModal()
    {
        $this->reset(['editingPostId', 'editTitle', 'editContent', 'editImage', 'title', 'content', 'image']);
        $this->showPostModal = true;
    }

    public function cancelEdit()
{
    // Reset all data and close module
    $this->reset(['editingPostId', 'editTitle', 'editContent', 'editImage', 'title', 'content', 'image', 'showPostModal']);
}

    public function getLikesCountProperty()
    {
        $postIds = Post::where('user_id', $this->user->id)->pluck('id');
        return Like::whereIn('post_id', $postIds)->count();
    }
    public function loadData()
{
    // Check database again and refresh data for component
    $this->isFriend = FriendList::where('user_id', auth()->id())
        ->where('friend_id', $this->user->id)
        ->exists();
}

    // --- Render Data ---
    public function render()
    {
        return view('livewire.user-profile', [
            'posts' => Post::where('user_id', $this->user->id)
                ->with(['likes.user', 'user'])
                ->latest()
                ->get()
        ]);
    }
}