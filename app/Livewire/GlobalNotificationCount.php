<?php

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
        return ChatMessage::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function mount()
    {
        $this->loginID = Auth::id();
        $this->count = $this->loadGlobalCount();
    }
    
    public function incrementCount(array $message)
    {
        if (isset($message['sender_id']) && (int) $message['sender_id'] !== (int) Auth::id()) {
            $this->count = $this->loadGlobalCount(); 
        }
    }

    #[On('chatRead')]
    public function handleChatRead()
    {
        $this->count = $this->loadGlobalCount();
    }

    public function render()
    {
        return view('livewire.global-notification-count');
    }
}