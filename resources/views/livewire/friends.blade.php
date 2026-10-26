<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">

    {{-- SEARCH BAR HEADER --}}
    <div class="w-full max-w-md">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass"
            placeholder="Search friends or discover people..." clearable />
    </div>

    {{-- USER GRID SECTION --}}
    <div class="grid auto-rows-min gap-4 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($users as $user)
            @php
                $isFriend = in_array($user->id, $friendIds);
            @endphp

            {{-- User Card --}}
            <div wire:key="user-{{ $user->id }}"
                class="relative rounded-xl border border-neutral-200 dark:border-neutral-700 p-4 bg-gradient-to-r from-cyan-500 to-blue-600 shadow-md">
                <div class="z-10 relative font-semibold text-xl text-[#e2eafc]">

                    <div class="flex items-center flex-col gap-4 min-h-full text-center">

                        {{-- Profile Image Display with Initials Fallback --}}
                        @if ($user->profile_image)
                            <img src="{{ $user->profile_image }}" alt="{{ $user->name }}'s Profile Photo"
                                class="w-20 h-20 lg:w-28 lg:h-28 aspect-square object-cover rounded-full ring-4 ring-[#e2eafc] dark:ring-blue-300 shadow-xl mb-2">
                        @else
                            <div
                                class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center text-3xl font-bold shadow-xl mb-2">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <h3>{{ $user->name }}</h3>
                        <h2 class="text-sm opacity-90">{{ $user->email }}</h2>

                        {{-- Dynamic Button / Status --}}
                        @if ($isFriend)
                            <span class="bg-emerald-500 text-white text-xs px-4 py-2 rounded-full font-semibold shadow">
                                Friends
                            </span>
                        @else
                            <button wire:click.prevent="addFriend({{ $user->id }})"
                                class="bg-[#e2eafc] hover:bg-blue-600 hover:text-[#e2eafc] text-blue-600 text-sm px-4 py-2 rounded-full transition shadow">
                                Add friend
                            </button>
                        @endif

                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-12 text-neutral-400">
                <p>No users found matching your search.</p>
            </div>
        @endforelse
    </div>
    {{-- PAGINATION LINKS --}}
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>