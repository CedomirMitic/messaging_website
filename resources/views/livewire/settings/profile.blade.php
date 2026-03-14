<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Traits\CloudinaryUpload; // Dodali smo naš Trait

new class extends Component {
    use WithFileUploads, CloudinaryUpload; // Koristimo WithFileUploads i CloudinaryUpload

    public string $name = '';
    public string $email = '';
    public $profileImage;
    public string $currentImagePath = '';
    public string $imageVersion = '';
    public ?string $tempProfileUrl = null;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->currentImagePath = $user->profile_image ?? '';
        $this->imageVersion = $user->updated_at?->timestamp ?? now()->timestamp;
    }

    /**
     * Livewire hook: Clear validation errors when a new file is selected.
     */
    public function updatedProfileImage(): void
    {
        $this->resetValidation('profileImage');
        $this->tempProfileUrl = null;
    }

    /**
     * Removes the current profile photo.
     */
    public function removeProfilePhoto(): void
    {
        $user = Auth::user();

        if ($user->profile_image) {
            // 1. Brišemo fizički fajl sa Cloudinary-ja (folder 'profile_photos')
            $this->deleteFromCloudinary($user->profile_image, 'profile_photos');

            // 2. Brišemo putanju iz baze
            $user->profile_image = null;
            $user->save();

            // Resetujemo stanje komponente
            $this->currentImagePath = '';
            $this->profileImage = null;
            $this->tempProfileUrl = null;
            $this->imageVersion = now()->timestamp;
            $this->dispatch('profile-updated', name: $user->name);
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
            'profileImage' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
        ]);

        if ($this->profileImage) {
            // 1. Ako već postoji slika, obriši staru da ne skupljamo smeće
            if ($user->profile_image) {
                $this->deleteFromCloudinary($user->profile_image, 'profile_photos');
            }

            // 2. Upload nove slike koristeći CURL metod iz Trait-a
            $path = $this->uploadToCloudinary($this->profileImage, 'profile_photos');

            if ($path) {
                $user->profile_image = $path;
                $this->currentImagePath = $path;
                $this->tempProfileUrl = $path;
            }

            $this->profileImage = null;
        }

        $user->name = $this->name;
        $user->email = $this->email;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->imageVersion = now()->timestamp;
        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();
        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full profile-section">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6" enctype="multipart/form-data">

            <div class="space-y-4">
                <p class="font-bold ">{{ __('Profile Photo') }}</p>
                <div class="flex items-center space-x-4">

                    @php
                        // Prioritet ima temp URL, pa onda putanja iz baze
                        $imageUrl = $tempProfileUrl ?: ($currentImagePath ?: null);
                    @endphp

                    @if ($imageUrl)
                        <img wire:key="profile-image-{{ $imageVersion }}" src="{{ $imageUrl }}" alt="Current Profile Photo"
                            class="w-20 h-20 object-cover rounded-full border border-zinc-200">
                    @else
                        <div
                            class="w-20 h-20 bg-zinc-200 rounded-full flex items-center justify-center text-zinc-500 text-xl font-bold">
                            {{ strtoupper(substr($name, 0, 1)) }}
                        </div>
                    @endif

                    <div class="flex flex-col space-y-2">
                        <flux:input wire:model="profileImage" :label="__('Choose Photo')" type="file" />

                        @if ($currentImagePath)
                            <flux:button type="button" variant="danger" wire:click="removeProfilePhoto"
                                class="w-fit text-sm">
                                {{ __('Remove Photo') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}
                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>
                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button"
                        wire:loading.attr="disabled" wire:target="updateProfileInformation, profileImage">

                        <span wire:loading.remove
                            wire:target="updateProfileInformation, profileImage">{{ __('Save') }}</span>

                        <span wire:loading wire:target="updateProfileInformation, profileImage"
                            class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 text-white mr-2 inline-block"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('Uploading to Cloud...') }}
                        </span>
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>