<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\FriendList;
use App\Events\MessageRead;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Livewire\GlobalNotificationCount;

class Chat extends Component
{
    // --- Livewire Properties ---
    public $users;
    public $selectedUser = null;
    public $newMessage;
    public $messages;

    public $profile_picture;
    public $loginID;
    public $unreadCounts = []; // Associative array: [sender_id => count]

    public $combined_id = null; // Channel name for the current active chat

    // --- Getters ---

    /**
     * Calculates the total number of unread messages across all users.
     * This is used for the global badge count in the navigation bar.
     */
    public function getGlobalCountProperty()
    {
        // Ensures we return 0 if the array is empty or not initialized
        if (empty($this->unreadCounts) || !is_array($this->unreadCounts)) {
            return 0;
        }
        return array_sum($this->unreadCounts);
    }

    // --- Helpers ---

    /**
     * Generates a consistent channel ID by sorting the two user IDs.
     */
    function generateCombinedId(int $id1, int $id2): string
    {
        $ids = [$id1, $id2];
        sort($ids); // Sort in ascending order
        return implode('_', $ids);
    }

    /**
     * Loads the list of friends/chat users based on the current FriendList.
     */
    private function loadUsersList()
    {
        $isFriendQuery = FriendList::select('friend_id')
            ->where('user_id', Auth::id());
        $this->users = User::whereIn("id", $isFriendQuery)->get();
    }

    /**
     * Private method that automatically adds the sender as a friend
     * if the current user has an unread message from them.
     */
    private function autoFriendSenders(array $senderIds): void
    {
        $currentUserId = Auth::id();

        foreach ($senderIds as $senderId) {
            // Check if the friendship (current user -> sender) already exists
            $exists = FriendList::where('user_id', $currentUserId)
                ->where('friend_id', $senderId)
                ->exists();

            if (!$exists) {
                // If it doesn't exist, automatically add the entry to the database
                FriendList::create([
                    'user_id' => $currentUserId, // Current user (the one who received the message)
                    'friend_id' => $senderId,    // Message sender
                ]);
            }
        }
    }

    /**
     * Loads the count of unread messages, grouped by sender ID, from the database.
     */
    private function loadUnreadMessageCount()
    {
        $unreadMessages = ChatMessage::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->get();

        // Extract unique IDs of all senders of unread messages
        $senderIds = $unreadMessages->pluck('sender_id')->unique()->toArray();

        //  Trigger automatic friend addition before loading the list
        $listWasUpdated = false;
        if (!empty($senderIds)) {
            // We need a way to know if a NEW FriendList entry was created.
            $initialUserCount = FriendList::where('user_id', Auth::id())->count();
            $this->autoFriendSenders($senderIds);

            $finalUserCount = FriendList::where('user_id', Auth::id())->count();
            if ($finalUserCount > $initialUserCount) {
                $listWasUpdated = true;
            }
        }

        // Continue counting unread messages
        $this->unreadCounts = $unreadMessages->groupBy('sender_id')
            ->map(fn($group) => $group->count())
            ->toArray();

        // Refresh the user list if a new friend was auto-added ***
        if ($listWasUpdated) {
            $this->loadUsersList();
        }
    }

    // --- Lifecycle Methods ---

    public function mount()
    {
        $this->loadUnreadMessageCount();
        $this->loadUsersList();
        $this->loginID = Auth::id();
    }

    public function render()
    {
        return view('livewire.chat');
    }

    // --- Actions ---

    /**
     * Selects a user, marks their unread messages as read, and updates counts.
     */
    public function selectUser($id)
    {
        $this->selectedUser = User::find($id);
        $this->combined_id = $this->generateCombinedId($this->loginID, $this->selectedUser->id);

        //  Mark unread messages from this sender as read in the database
        $unreadMessages = ChatMessage::where('receiver_id', Auth::id())
            ->where('sender_id', $this->selectedUser->id)
            ->whereNull('read_at')
            ->get();

        //  Update database Messages and broadcast read status
        $unreadMessages->each(function ($message) {
            $message->read_at = now();
            $message->save();

            //  Broadcast event to update the other user's UI (read receipts)
            broadcast(new MessageRead($message, $this->combined_id));
        });

        // Load user messages
        $this->loadMessages();

        //  Update the individual user counts (removes count for the selected user)
        $this->loadUnreadMessageCount();

        // Dispatch event to GlobalNotificationCount for it to update the globalCount
        $this->dispatch('chatRead')->to(GlobalNotificationCount::class);

        $this->dispatch('chatChannelChanged', [
            'combined_id' => $this->combined_id,
            'userName' => Auth::user()->name,
            'selected_user_id' => $this->selectedUser->id
        ]);
        // Dispatch a simple event telling JS to scroll.
        $this->dispatch('scroll-to-last');
    }

    /**
     * Loads messages for the currently selected chat partner.
     */
    public function loadMessages()
    {
        $this->messages = ChatMessage::query()
            ->where(function ($q) {
                $q->where("sender_id", Auth::id())
                    ->where("receiver_id", $this->selectedUser->id);
            })
            ->orWhere(function ($q) {
                $q->where("sender_id", $this->selectedUser->id)
                    ->where("receiver_id", Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Broadcasts a typing notification event.
     */
    public function userTyping()
    {
        $this->dispatch("userTyping", [
            "userID" => $this->loginID,
            "selectedUserID" => $this->selectedUser->id,
            "userName" => Auth::user()->name,
            "combined_id" => $this->combined_id
        ]);
    }

    /**
     * (event for the sender)
     * Submits a new message.
     */
    public function submit()
    {
        if (!$this->newMessage)
            return;

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUser->id,
            'message' => $this->newMessage
        ]);

        $this->messages->push($message);
        $this->newMessage = '';

        $this->combined_id = $this->generateCombinedId($message->sender_id, $message->receiver_id);

        // Broadcast the message event to both users
        broadcast(new MessageSent($message, $this->combined_id));

        // Dispatch a simple event telling JS to scroll.(not needed but faster update)
        $this->dispatch('scroll-to-last');
    }

    // --- Livewire Event Listeners ---

    /**
     * (event for receiver)
     * Handles an incoming message event when the chat IS currently open.
     * Updates the chat view, marks the message as read, and sends a read receipt.
     */
    public function newChatMessageNotification($message)
    {
        // Only proceed if the message is from the user we are actively chatting with
        if ($message['sender_id'] == $this->selectedUser->id) {
            $messageObj = ChatMessage::find($message['id']);
            $this->messages->push($messageObj);

            // Mark as read in database
            $messageObj->read_at = now();
            $messageObj->save();

            // Broadcast event to user that the message has been read
            broadcast(new MessageRead($messageObj, $this->combined_id));

        }
        // Scrool the chat to bottom
        $this->dispatch('scroll-to-last');
    }

    /**
     * Handles an incoming read receipt event.
     * Updates the UI to show the message was read.
     */
    #[On('echo-private:chat.{loginID},MessageRead')]
    public function messageReadNotification($message)
    {
        // Check if the read receipt is for a message we sent
        if ($message['sender_id'] == $this->loginID) {
            // Find the message in the collection by its ID
            $index = $this->messages->search(function ($msg) use ($message) {
                return $msg->id == $message['id'];
            });

            if ($index !== false) {
                // Update the 'read_at' property in the messages list to update the UI
                $this->messages[$index]->read_at = $message['read_at'];
            }
        }
    }

    /**
     * Handles an incoming message event when the chat IS NOT currently open.
     * Only updates the unread message count for the global badge and user list.
     */
    #[On('echo-private:chat.{loginID},MessageSent')]
    public function newMessageNotification(array $message)
    {
        $senderId = $message['sender_id'];

        if ($senderId != Auth::id()) {

            $isSenderInList = $this->users->contains('id', $senderId);

            // CRITICAL CHECK: If chat IS OPEN with this sender, DO NOT count it.
            if (isset($this->selectedUser) && $this->selectedUser->id == $senderId) {
                return;
            }
            if (!$isSenderInList) {
                // A new sender has appeared! We need to auto-friend them and refresh the list.

                // Manually run the auto-friend logic for this new sender
                $exists = FriendList::where('user_id', Auth::id())
                    ->where('friend_id', $senderId)
                    ->exists();

                if (!$exists) {
                    FriendList::create([
                        'user_id' => Auth::id(),
                        'friend_id' => $senderId,
                    ]);
                }

                //  Refresh the UI list
                $this->loadUsersList();
            }

            // Increment the individual user's unread count
            $currentCount = $this->unreadCounts[$senderId] ?? 0;
            $this->unreadCounts[$senderId] = $currentCount + 1;

        }
    }
}