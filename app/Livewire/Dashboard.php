<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;
use App\Models\FriendList;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Traits\CanLikePosts;
use App\Traits\ManagesFriends;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    use CanLikePosts, ManagesFriends;
    use WithFileUploads;

    // --- Properties ---
    public $posts;
    public string $activeTab = 'discover'; // Tracks 'discover' or 'friends'
    public bool $showPostModal = false;

    /**
     * Initialize the component
     */
    public function mount()
    {
        $this->loadData();
    }

    /**
     * Centralized method to load posts based on current state
     */
    #[On('post-created')]
    public function loadData()
    {
        $currentUserId = Auth::id();

        // Get IDs of all current friends
        $friendIds = FriendList::where('user_id', $currentUserId)
            ->pluck('friend_id')
            ->toArray();

        // Load posts based on the active tab
        $postQuery = Post::latest()->with('user');

        if ($this->activeTab === 'friends') {
            // Show only posts from friends
            $postQuery->whereIn('user_id', $friendIds);
        } else {
            // Discover: Show posts from people NOT in my friend list and NOT myself
            $postQuery->whereNotIn('user_id', $friendIds)
                ->where('user_id', '!=', $currentUserId);
        }

        $this->posts = $postQuery->get();
    }

    /**
     * Switch between Discover and Friends tabs
     */
    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->loadData();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}