<?php

namespace App\Traits;

use App\Models\FriendList;
use Illuminate\Support\Facades\Auth;

trait ManagesFriends
{
    /**
     * Add a friend to the user's friend list.
     */
    public function addFriend($friendId)
    {
        $userId = Auth::id();

        // Prevent adding yourself
        if ($userId == $friendId) {
            $this->dispatch('notify', message: 'You cannot add yourself!', type: 'error');
            return;
        }

        // Prevent duplicates
        $exists = FriendList::where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->exists();

        if (!$exists) {
            FriendList::create([
                'user_id' => $userId,
                'friend_id' => $friendId
            ]);

            $this->dispatch('notify', message: 'Friend added successfully!');
            $this->dispatch('close-modal'); // Šaljemo signal za zatvaranje

            // Refresh component data
            if (method_exists($this, 'loadData')) {
                $this->loadData();
            }
        } else {
            $this->dispatch('notify', message: 'Already friends!', type: 'info');
        }
    }

    /**
     * Remove a friend from the user's friend list.
     */
    public function removeFriend($friendId)
    {
        $userId = Auth::id();

        $deleted = FriendList::where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->delete();

        if ($deleted) {
            $this->dispatch('notify', message: 'Friend removed.', type: 'info');
            $this->dispatch('close-modal', name: 'confirm-unfriend');

            if (method_exists($this, 'loadData')) {
                $this->loadData();
            }
        } else {
            $this->dispatch('notify', message: 'Friendship not found.', type: 'error');
        }
    }
}