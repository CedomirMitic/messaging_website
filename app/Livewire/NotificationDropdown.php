<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    
    public function handleNewNotification()
    {
        
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.notification-dropdown', [
            'notifications' => Auth::user()->notifications()->latest()->take(10)->get(),
            'unreadCount' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}