{{-- resources/views/livewire/dashboard.blade.php --}}

<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">

    {{-- 1. USER GRID SECTION --}}
    <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($users as $user)
            {{-- User Card --}}
            <div wire:key="user-{{ $user->id }}"
                class="relative rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-gradient-to-r from-cyan-500 to-blue-600">
                <div class="z-10 relative font-semibold text-xl text-[#e2eafc] ">

                    <div class="flex items-center flex-col gap-4 min-h-full">

                        {{-- Profile Image Display with Initials Fallback --}}
                        @if ($user->profile_image)
                            <img src="{{ $user->profile_image }}" alt="{{ $user->name }}'s Profile Photo"
                                class="w-20 h-20 lg:w-40 lg:h-40 aspect-square object-cover rounded-full ring-4 ring-[#e2eafc] dark:ring-blue-300 shadow-xl mb-2">
                        @else
                            <div
                                class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center text-3xl font-bold shadow-xl mb-2">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <h3> {{ $user->name }} </h3>
                        <h2 class="text-sm"> {{ $user->email }} </h2>

                        <button wire:click.prevent="addFriend({{ $user->id }})"
                            class="bg-[#e2eafc] hover:bg-blue-600 hover:text-[#e2eafc] text-blue-600 text-sm px-4 py-2 rounded-full transition">
                            Add friend
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{-- 2. POST FEED SECTION --}}
    <div
        class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800">

        {{-- B. Tab Switcher (Placed below the button) --}}
        <div class="flex border-b dark:border-neutral-700 bg-gray-50/50 dark:bg-neutral-800/50">
            <button wire:click="setTab('discover')"
                class="flex-1 py-3 text-sm font-semibold transition-all duration-200 relative {{ $activeTab === 'discover' ? 'text-blue-600' : 'text-neutral-500 hover:text-neutral-700' }}">
                Discover
                @if($activeTab === 'discover')
                    <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600"></div>
                @endif
            </button>

            <button wire:click="setTab('friends')"
                class="flex-1 py-3 text-sm font-semibold transition-all duration-200 relative {{ $activeTab === 'friends' ? 'text-blue-600' : 'text-neutral-500 hover:text-neutral-700' }}">
                Friends Feed
                @if($activeTab === 'friends')
                    <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600"></div>
                @endif
            </button>
        </div>

        {{-- C. Loading Indicator (Shows briefly while filtering) --}}
        <div wire:loading wire:target="setTab" class="w-full text-center py-4">
            <span class="text-xs text-blue-500 animate-pulse font-medium">Updating feed...</span>
        </div>

        {{-- D. Scrollable Feed --}}
        <div class="relative z-10 p-4 space-y-4 overflow-y-auto" style="max-height: 70vh;">

            @foreach ($posts as $post)
    <div wire:key="post-{{ $post->id }}"
        class="p-4 rounded-lg shadow-sm border border-neutral-100 dark:border-neutral-700 bg-gray-50 dark:bg-neutral-700/50">

        {{-- User Info with Avatar and Hover Popup --}}
        <div class="flex items-center space-x-3 mb-3" x-data="{ open: false }">
            {{-- Avatar --}}
            <div class="flex-shrink-0">
                @if ($post->user && $post->user->profile_image)
                    <img src="{{ $post->user->profile_image }}" 
                         class="w-10 h-10 object-cover rounded-full border border-neutral-200 dark:border-neutral-600 shadow-sm">
                @else
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-sm">
                        {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
            </div>

            {{-- Name and Timing --}}
            <div class="relative">
                <div class="flex flex-col">
                    <span 
                        @mouseenter="open = true" 
                        @mouseleave="open = false"
                        class="font-semibold text-neutral-900 dark:text-white cursor-pointer hover:text-blue-600 transition-colors">
                        {{ $post->user->name ?? 'Unknown User' }}
                    </span>
                    <span class="text-xs text-neutral-500 dark:text-neutral-400">
                        {{ $post->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- Hover Profile Card --}}
                <div 
                    x-show="open" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    @mouseenter="open = true"
                    @mouseleave="open = false"
                    class="absolute z-50 mt-2 w-56 p-4 bg-white dark:bg-neutral-800 rounded-xl shadow-2xl border border-neutral-200 dark:border-neutral-700 left-0"
                    style="display: none;">
                    
                    <div class="flex flex-col items-center text-center">
                        @if ($post->user && $post->user->profile_image)
                            <img src="{{  $post->user->profile_image }}" class="w-14 h-14 rounded-full object-cover mb-2 ring-2 ring-blue-500/20">
                        @else
                            <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center text-xl font-bold mb-2">
                                {{ strtoupper(substr($post->user->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        
                        <h4 class="font-bold text-neutral-900 dark:text-white">{{ $post->user->name }}</h4>
                        <p class="text-[10px] text-neutral-500 mb-4 truncate w-full">{{ $post->user->email }}</p>
                        
                        {{-- This creates /profile/7 instead of /profile?user=7 --}}
                        <a href="{{ route('profile', $post->user->id) }}" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs py-2 rounded-lg transition font-semibold shadow-md shadow-blue-500/20">
                            View Profile
                        </a>

                    </div>
                </div>
            </div>
        </div>

        {{-- Post Content --}}
        <h1 class="text-xl font-bold text-neutral-900 dark:text-white mb-2">{{ $post->title }}</h1>

        @if($post->image)
            {{-- Izbacio sam mx-auto i dodao max-w-md koji sada stoji fiksno levo --}}
            <div class="mt-3 w-full max-w-md overflow-hidden rounded-lg border dark:border-neutral-600 mb-3">
                <img src="{{ $post->image }}" 
                        class="w-full h-auto object-contain bg-zinc-50"
                        alt="{{ $post->title }}">
            </div>
        @endif

        <p class="text-neutral-700 dark:text-neutral-300 leading-relaxed">
            {{ $post->content }}
        </p>

        <div class="mt-4 pt-4 border-t dark:border-neutral-700 flex items-center">
            <button wire:click="toggleLike({{ $post->id }})"
                class="flex items-center space-x-2 {{ $post->isLikedBy(auth()->id()) ? 'text-red-500' : 'text-neutral-500' }} hover:scale-105 transition">
                <flux:icon.heart variant="{{ $post->isLikedBy(auth()->id()) ? 'solid' : 'outline' }}"
                    class="w-6 h-6" />
                <span class="font-bold">{{ $post->likes()->count() }}</span>
            </button>
        </div>
    </div>
@endforeach

            @if ($posts->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 opacity-60">
                    <svg class="w-12 h-12 mb-3 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <p class="text-neutral-500 dark:text-neutral-400">
                        {{ $activeTab === 'friends' ? 'Your friends haven\'t posted anything yet.' : 'No new posts to discover right now!' }}
                    </p>
                </div>
            @endif
        </div>
    </div>