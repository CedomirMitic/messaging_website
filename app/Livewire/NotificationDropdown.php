<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    // Remove the protected $listeners array and replace it with this:
    public function getListeners()
    {
        return [
            // This dynamically injects the actual User ID into the listener string
            "echo-private:App.Models.User." . Auth::id() . ",NotificationSent" => '$refresh',
        ];
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        // Adding this ensures that every poll fetches fresh data from the DB
        return view('livewire.notification-dropdown', [
            'notifications' => Auth::user()->notifications()->latest()->take(10)->get(),
            'unreadCount' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}