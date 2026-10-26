<div>
    <flux:navlist.item icon="chat-bubble-oval-left-ellipsis" :href="route('chat')" :current="request()->routeIs('chat')" class="w-full">
        <div class="flex items-center justify-between w-full pr-2">
            <span>{{ __('Chat') }}</span>

            @if($count > 0)
                <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                    {{ $count }}
                </span>
            @endif
        </div>
    </flux:navlist.item>
</div>

@script
<script>
    document.addEventListener('livewire:initialized', () => {
        let loginID = @this.get('loginID');

        if (loginID) {
            // Slušamo tačan privatni kanal korisnika (npr. chat.6)
            window.Echo.private(`chat.${loginID}`)
                .listen('MessageSent', (event) => {
                    console.log('Globalni bedžuhvatio poruku:', event);
                    // Pozivamo PHP metodu definisanu u klasi
                    @this.call('incrementCount', event);
                });
        }
    });
</script>
@endscript