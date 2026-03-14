<span id="global-chat-count"
    class="absolute -top-1 right-1 bg-red-500 text-white px-1.5 py-0.5 rounded-full text-xs font-semibold z-10 
           @if ($count === 0) hidden @endif">
    @if ($count > 0)
        {{ $count }}
    @endif
</span>