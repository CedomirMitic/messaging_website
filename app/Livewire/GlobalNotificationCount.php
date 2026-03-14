<?php
// app/Livewire/GlobalNotificationCount.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;

class GlobalNotificationCount extends Component
{
    public $count = 0;
    public $loginID;

    private function loadGlobalCount()
    {
        // Get all unread messages where user is receiver
        $unreadCounts = ChatMessage::selectRaw('count(*) as count')
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->value('count'); // Take only count
            
        return $unreadCounts ?? 0;
    }

    public function mount()
    {
        $this->loginID = Auth::id();
        $this->count = $this->loadGlobalCount();
    }
    
    // Listen to the broadcast of message sent and execute the method on it
    #[On('echo-private:chat.{loginID},MessageSent')]
    public function handleNewMessageBroadcast(array $message)
    {
        $senderId = $message['sender_id'];
        
        // Check if message is read already if chat is opened
        $messageIsRead = ChatMessage::where('id', $message['id'])
            ->whereNotNull('read_at')
            ->exists();
            
        // If message isnt read increment the count
        if ($senderId !== Auth::id() && !$messageIsRead) {
            
            // Define the count again
            $this->count = $this->loadGlobalCount(); 
        }
    }
    // Listener connected to chat.php when user is selected for updating globalcount
    #[On('chatRead')]
    public function handleChatRead()
    {
        // Ponovo učitajte ukupan brojač iz baze
        $this->count = $this->loadGlobalCount();
    }

    public function render()
    {
        return view('livewire.global-notification-count');
    }
}