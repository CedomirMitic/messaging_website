<div class="relative" x-data="{ open: false }">

    {{-- Notification Trigger --}}
    <flux:navlist.item icon="bell-alert" @click.prevent="open = !open" class="cursor-pointer relative z-[101]">
        {{ __('Notification') }}

        @if($unreadCount > 0)
            <span class="absolute right-2 top-3 flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span
                    class="relative inline-flex rounded-full h-2 w-2 bg-red-600 shadow-[0_0_8px_rgba(220,38,38,0.8)]"></span>
            </span>
        @endif
    </flux:navlist.item>

    {{-- Dropdown Menu --}}
    <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-[-10px]" x-transition:enter-end="opacity-100 translate-y-0"
        class="fixed mt-1 w-80 z-[999] bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-neutral-700 shadow-2xl rounded-xl overflow-hidden"
        @click.stop>

        <div
            class="p-4 border-b dark:border-neutral-700 flex justify-between items-center bg-gray-50/50 dark:bg-neutral-900/50">
            <h3 class="font-bold text-sm dark:text-white">Recent Activity</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" @click="open = false"
                    class="text-[10px] text-blue-600 font-bold uppercase tracking-wider hover:underline">
                    Mark all as read
                </button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div
                    class="p-4 border-b dark:border-neutral-700 last:border-0 hover:bg-gray-50 dark:hover:bg-neutral-700/50 transition cursor-default">
                    <p class="text-sm dark:text-neutral-200 leading-tight">
                        @if(isset($notification->data['user_id']))
                            <a href="{{ route('profile', ['id' => $notification->data['user_id']]) }}" wire:navigate
                                class="font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                                {{ $notification->data['user_name'] ?? 'User' }}
                            </a>
                        @else
                            <span class="font-bold text-blue-600">
                                {{ $notification->data['user_name'] ?? 'User' }}
                            </span>
                        @endif

                        liked:
                        <span
                            class="italic font-medium">"{{ Str::limit($notification->data['post_title'] ?? '', 25) }}"</span>
                    </p>
                    <span class="text-[10px] text-neutral-500 mt-1 block">
                        {{ $notification->created_at->diffForHumans() }}
                    </span>
                </div>
            @empty
                <div class="p-8 text-center text-neutral-500 text-sm italic">
                    No new notifications
                </div>
            @endforelse
        </div>
    </div>
</div>

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        let userId = @json(auth()->id());

        if (userId) {
            window.Echo.private(`chat.${userId}`)
                .listen('.PostLikedEvent', (event) => {
                    console.log('Stigla notifikacija preko chat kanala:', event);
                    @this.$refresh();
                });
        }
    });
</script>
@endscript