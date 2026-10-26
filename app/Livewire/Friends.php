<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination; // 1. Include pagination trait
use App\Models\FriendList;
use App\Traits\ManagesFriends;
use Illuminate\Support\Facades\Auth;

class Friends extends Component
{
    use ManagesFriends, WithPagination;

    public string $search = '';

    // Reset pagination when user types in the search bar
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $currentUserId = Auth::id();
        
        $friendIds = FriendList::where('user_id', $currentUserId)
            ->pluck('friend_id')
            ->toArray();

        $users = User::where('id', '!=', $currentUserId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->orderByRaw($friendIds ? "CASE WHEN id IN (" . implode(',', $friendIds) . ") THEN 0 ELSE 1 END" : "id")
            ->orderBy('name', 'asc')
            ->paginate(12); // <-- Limits to 12 users per page

        return view('livewire.friends', [
            'users' => $users,
            'friendIds' => $friendIds,
        ]);
    }
}