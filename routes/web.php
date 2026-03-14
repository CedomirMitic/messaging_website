<?php

use App\Livewire\Chat;
use Livewire\Volt\Volt;
use App\Livewire\Profile;
use App\Livewire\Dashboard;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
Broadcast::routes(['middleware' => ['web', 'auth']]);

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
Route::get('chat', Chat::class)
    ->middleware(['auth', 'verified'])
    ->name('chat');

Route::get('/profile/{id?}', Profile::class)
    ->middleware(['auth'])
    ->name('profile');