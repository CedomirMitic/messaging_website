<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Chat') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Lets Chat!') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
    <div class="flex h-[550px] text-sm border rounded-xl shadow overflow-hidden">
        <div class="w-1/4 border-r bg-blue-600 text-[#e2eafc] flex flex-col">
            <div class="p-4 font-bold border-b flex-shrink-0">Users</div>
            <div class="divide-y overflow-y-scroll flex-grow custom-scrollbar">
                @foreach ($users as $user)
                    <div wire:click="selectUser({{ $user->id }})"
                        class="p-3 cursor-pointer hover:bg-blue-800 transition{{ $selectedUser && $selectedUser->id === $user->id ? ' bg-blue-900 rounded-xl font-semibold' : '' }}">

                        {{-- Unread chat messages count for the user --}}
                        @if (isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0)
                            <span class="ml-auto bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">
                                {{ $unreadCounts[$user->id] }}
                            </span>
                        @endif

                        {{-- Profile Image/Initial --}}
                        @if ($user->profile_image)
                            <img src="{{ $user->profile_image }}" alt="{{ $user->name }}'s Profile Photo"
                                class="w-15 h-15 lg:w-15 lg:h-15 aspect-ratio:auto object-cover rounded-full ring-4 ring-[#e2eafc] dark:ring-blue-300 shadow-xl mb-2">
                        @else
                            <div
                                class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center text-3xl font-bold shadow-xl mb-2">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        <div>{{ $user->name }}</div>
                        <div class="text-xs">{{ $user->email }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="w-3/4 flex flex-col">

            {{-- START OF CONDITIONAL CHAT DISPLAY: Only show chat if a user is selected --}}
            @if ($selectedUser)

                <div class="p-4 border-b bg-blue-500 text-[#e2eafc]">
                    {{-- Profile Image Display with Initials Fallback --}}
                    @if ($selectedUser->profile_image)
                        <img src="{{ $selectedUser->profile_image }}"
                            alt="{{ $selectedUser->name }}'s Profile Photo"
                            class="w-15 h-15 lg:w-15 lg:h-15 aspect-ratio:auto object-cover rounded-full ring-4 ring-[#e2eafc] dark:ring-blue-300 shadow-xl mb-2">
                    @else
                        <div
                            class="w-15 h-15 bg-blue-600 rounded-full flex items-center justify-center text-3xl font-bold shadow-xl mb-2">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-lg font-semibold">{{ $selectedUser->name }}</div>
                    <div class="text-xs">{{ $selectedUser->email }}</div>
                </div>

                <div class="flex-1 p-4 overflow-y-auto custom-scrollbar bg-[#e2eafc] flex flex-col" id="messages-container" {{--
                    THE FIX: When this element is initialized (appears in the DOM), scroll to bottom. --}} x-init="() => {     
                                        const el = $el; 
                                        // Wrap in requestAnimationFrame for reliability
                                        requestAnimationFrame(() => { 
                                            el.scrollTop = el.scrollHeight; 
                                        });
                                    }">
                    @foreach ($messages as $message)
                        <div class="flex w-full mb-2 {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">

                            <div
                                class="flex flex-col {{ $message->sender_id === auth()->id() ? 'items-end' : 'items-start' }}">
                            <div class="max-w-xs px-4 py-2 rounded-2xl shadow {{ $message->sender_id === auth()->id() ? 'bg-blue-600' : 'bg-gray-500' }} text-white">
                                {{ $message->message }}
                            </div>

                            @if($loop->last && $message->sender_id == auth()->id())
                                <div id="message_read_{{ $message->id }}"
                                    class="mt-1 flex items-center">
                                    @if($message->read_at)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="green" class="size-6 text-green-500">
                                            <path fill-rule="evenodd"
                                                d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="size-6 text-gray-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    @endif
                                </div>
                            @endif

                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="typing-indicator" class="px-4 pb-1 text-xs bg-[#e2eafc] text-gray-400 italic"></div>
                <form wire:submit.prevent="submit" class="p-4 border-t dark:bg-white flex items-center gap-2">

                    {{-- Input field: wire:model tracks the message content. .debounce.300ms prevents spamming the
                    'userTyping' method --}}
                    <input wire:model.live="newMessage" wire:keydown.debounce.300ms="userTyping" type="text"
                        class="flex-1 border border-gray-300 text-black rounded-full px-4 py-2" placeholder="Type a message..." />

                    <button type="submit" {{-- Disable button if the input field is empty (client-side prevention of empty
                        requests) --}} @if(empty($newMessage)) disabled @endif {{-- Disable button while the submit action
                        is loading --}} wire:loading.attr="disabled" wire:target="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded-full transition">

                        {{-- Default text when not loading --}}
                        <span wire:loading.remove wire:target="submit">
                            Send
                        </span>

                        {{-- Loading indicator when submitting --}}
                        <span wire:loading wire:target="submit" class="flex items-center justify-center gap-2">
                            {{-- SVG Spinner (Tailwind CSS animation classes are used for the spin effect) --}}
                            <svg class="animate-spin text-white h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </form>

            @else
                {{-- Placeholder message when no user is selected --}}
                <div class="flex-1 flex items-center justify-center text-gray-500 bg-gray-100">
                    <p class="text-lg">Select a user to start chatting.</p>
                </div>
            @endif
            {{-- END OF CONDITIONAL CHAT DISPLAY --}}
        </div>
    </div>
</div>
@vite('resources/js/app.js')
@script
<script>
    // Global variable to store the current channel subscription reference
    window.current_channel_subscription = null;
    window.current_selected_user_id = null;


    // handle the actual scroll action
    function scroolToBottom() {
        const el = document.getElementById('messages-container');
        if (el) {
            //  Defer the scroll until the next browser repaint cycle.
            requestAnimationFrame(() => {
                el.scrollTop = el.scrollHeight;
            });
        }
    }

    // UNIQUE FUNCTION DEFINITION - set globally (outside livewire:initialized)
    function subscribeToChatChannel(combinedId, currentLoginID) {
        if (!combinedId) return;

        const channelName = `chat.${combinedId}`;

        // If the channel is already active, do nothing
        if (window.current_channel_subscription === channelName) {
            console.log(`Already subscribed to ${channelName}. Skipping re-subscription.`);
            return;
        }

        // ...
        //  Leave the old channel (if subscribed)
        if (window.current_channel_subscription) {
            try {
                window.Echo.leave(window.current_channel_subscription);
                console.log(`Previous subscription left: ${window.current_channel_subscription}`);
            } catch (error) {
                // If channel doesn't exist console the error
                console.warn(`Could not leave channel: ${window.current_channel_subscription}`);
            }
        }
        // ...

        //  New Private Channel Subscription (for messages and whispers)
        window.Echo.private(channelName)
            // ... unutar window.Echo.private(channelName) ...

            .listenForWhisper('typing', (event) => {
                // TEST console.log('Whisper Typing event received on channel:', channelName, event);

                // Fetch the ID of the currently selected chat partner from global variable
                const selectedUserID = window.current_selected_user_id;

                // Check if the event is from another user AND if the sender is the actively selected chat partner
                if (String(event.userID) !== String(currentLoginID) && String(event.userID) === String(selectedUserID)) {

                    const t = document.getElementById('typing-indicator');
                    if (t) {
                        t.innerText = `${event.userName} is Typing...`;

                        // Clear indicator after 5 seconds
                        setTimeout(() => {
                            // Only clear the indicator if it still shows the current user is typing
                            if (t.innerText.startsWith(event.userName)) {
                                t.innerText = '';
                            }
                        }, 3000);
                    }
                }
            })// listener for new message received
            .listen('MessageSent', (event) => {
                // TEST console.log('New Message received on channel:', event);

                // Calling method through livewire to update UI 
                @this.call('newChatMessageNotification', event);
            })
            .listen('MessageRead', (event) => {
                const currentLoginID = @this.get('loginID');

                // TEST  console.log('--- MESSAGE READ EVENT RECEIVED! ---', event); 

                // Check if we are the sender of the message and if read is true
                if (event.read_at && event.sender_id == currentLoginID) {

                    @this.call('messageReadNotification', event);

                }
            });

        //  Store the new subscription reference
        scroolToBottom();
        window.current_channel_subscription = channelName;
        console.log(`New subscription established on: ${channelName}`);
    }; // <-- END OF THE SINGLE FUNCTION DEFINITION

    document.addEventListener('livewire:initialized', () => {

        // Get initial properties.
        let initial_combined_id = @this.get('combined_id');
        let initial_selected_user_id = @this.get('selectedUser.id');
        let loginID = @this.get('loginID');

        /* TEST console.log('Livewire Initialized. Initial combined ID:', initial_combined_id);
        console.log('Current User ID (loginID):', loginID); */


        // --- INITIALIZATION: Subscribe to the starting channel ---
        // We call the GLOBAL function we defined above
        if (initial_combined_id) {
            subscribeToChatChannel(initial_combined_id, loginID);
            // If you were successfully auto-scrolling here, the logic is now inside subscribeToChatChannel.
        }


        // --- LIVEWIRE LISTENER: Update channel when user changes ---
        Livewire.on('chatChannelChanged', (event) => {
            // Livewire v3 payload is in an array
            const data = event[0];
            subscribeToChatChannel(data.combined_id, loginID);
            //Save the selected user id
            window.current_selected_user_id = data.selected_user_id;
        });

        // Scrool

        Livewire.on('scroll-to-last', () => {
            scroolToBottom();
        });

        // --- LIVEWIRE LISTENER: SEND 'TYPING' WHISPER ---
        Livewire.on('userTyping', (dataArray) => {
            // ... Code for sending the Whisper ...
            const data = dataArray[0];

            if (data && data.combined_id) {

                // TEST console.log(`ATTEMPTING WHISPER from User ${data.userID} to channel: chat.${data.combined_id}`);

                window.Echo.private(`chat.${data.combined_id}`)
                    .whisper('typing', {
                        userID: data.userID,
                        userName: data.userName
                    });
            } else {
                console.error('userTyping event data is incomplete or missing combined_id.');
            }
        })

    });

</script>
@endscript