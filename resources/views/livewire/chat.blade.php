<div class="flex h-[75vh] text-sm border border-neutral-200 dark:border-neutral-700 rounded-xl shadow-sm overflow-hidden bg-white dark:bg-neutral-800"
    x-data="{ sidebarOpen: {{ $selectedUser ? 'false' : 'true' }} }">

    {{-- LEFT SIDE: Users List --}}
    <div class="flex flex-col border-r border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900 transition-all duration-300 relative z-20 h-full bg-white dark:bg-neutral-900 flex-shrink-0"
        :class="sidebarOpen ? 'w-80' : 'w-0 overflow-hidden'">

        <div
            class="p-4 font-bold border-b border-neutral-200 dark:border-neutral-700 flex-shrink-0 text-neutral-900 dark:text-white flex items-center justify-between whitespace-nowrap">
            <span>Users</span>
            {{-- Close button for sidebar --}}
            <button @click="sidebarOpen = false" class="text-neutral-500 hover:text-neutral-700">
                ✕
            </button>
        </div>

        <div
            class="divide-y divide-neutral-200 dark:divide-neutral-800 overflow-y-auto flex-grow custom-scrollbar whitespace-nowrap w-80">
            @foreach ($users as $user)
                @php 
                    $isSelected = $selectedUser && $selectedUser->id === $user->id; 
                @endphp


                <div wire:click="selectUser({{ $user->id }})" @click="sidebarOpen = false"
                    class="p-3 cursor-pointer flex items-center gap-3 transition-colors {{ $isSelected ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-neutral-100 dark:hover:bg-neutral-800 text-neutral-800 dark:text-neutral-200' }}">

                    {{-- Profile Image / Initial --}}
                    <div class="flex-shrink-0 relative">
                        @if ($user->profile_image)
                            <img src="{{ $user->profile_image }}" alt="{{ $user->name }}"
                                class="w-10 h-10 object-cover rounded-full ring-2 {{ $isSelected ? 'ring-white/40' : 'ring-neutral-200 dark:ring-neutral-700' }}">
                        @else
                            <div
                                class="w-10 h-10 {{ $isSelected ? 'bg-white text-blue-600' : 'bg-blue-600 text-white' }} rounded-full flex items-center justify-center font-bold text-sm shadow-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif

                        {{-- Unread Badge --}}
                        @if (isset($unreadCounts[$user->id]) && $unreadCounts[$user->id] > 0)
                            <span
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                {{ $unreadCounts[$user->id] }}
                            </span>
                        @endif
                    </div>

                    {{-- User Details --}}
                    <div class="flex flex-col overflow-hidden">
                        <span class="truncate text-sm">{{ $user->name }}</span>
                        <span
                            class="text-xs truncate {{ $isSelected ? 'text-blue-100' : 'text-neutral-500 dark:text-neutral-400' }}">{{ $user->email }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- RIGHT SIDE: Chat Window --}}
    <div class="flex-1 flex flex-col bg-white dark:bg-neutral-800 min-w-0 w-full transition-opacity duration-150"
        wire:loading.class="opacity-50" wire:target="selectUser">


        @if ($selectedUser)
            <div
                class="p-4 border-b border-neutral-200 dark:border-neutral-700 flex items-center justify-between bg-neutral-50/50 dark:bg-neutral-800/50">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-600 dark:text-neutral-300 transition"
                        title="Toggle Users List">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    @if ($selectedUser->profile_image)
                        <img src="{{ $selectedUser->profile_image }}" alt="{{ $selectedUser->name }}"
                            class="w-10 h-10 object-cover rounded-full ring-2 ring-neutral-200 dark:ring-neutral-700">
                    @else
                        <div
                            class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($selectedUser->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="font-bold text-neutral-900 dark:text-white">{{ $selectedUser->name }}</div>
                        <div class="text-xs text-neutral-500 dark:text-neutral-400">{{ $selectedUser->email }}</div>
                    </div>
                </div>
            </div>

            {{-- Messages Container --}}
            <div class="flex-1 p-4 overflow-y-auto custom-scrollbar bg-neutral-50/30 dark:bg-neutral-900/30 flex flex-col space-y-2"
                id="messages-container" x-init="() => { 
                                        const el = $el; 
                                        requestAnimationFrame(() => { 
                                            el.scrollTop = el.scrollHeight; 
                                        });
                                    }">

                @php
                    // Find the last message sent by the logged-in user in the current conversation collection
                    $lastAuthMessage = $messages->where('sender_id', auth()->id())->last();
                @endphp

                @foreach ($messages as $message)
                    <div class="flex w-full mb-1 {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="flex flex-col {{ $message->sender_id === auth()->id() ? 'items-end' : 'items-start' }}">
                            <div
                                class="max-w-md px-4 py-2 rounded-2xl shadow-sm text-sm {{ $message->sender_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-neutral-200 dark:bg-neutral-700 text-neutral-900 dark:text-white' }}">
                                {{ $message->message }}
                            </div>

                            {{-- Show Read/Sent status ONLY on the absolute last message sent by the auth user --}}
                            @if ($message->sender_id === auth()->id() && $lastAuthMessage && $message->id === $lastAuthMessage->id)
                                <span class="text-[10px] text-neutral-400 mt-0.5 px-1">
                                    @if ($message->read_at)
                                        <span class="text-blue-500 font-medium">Read</span>
                                    @else
                                        <span>Sent</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="typing-indicator" class="px-4 py-1 text-xs bg-neutral-50 dark:bg-neutral-800 text-neutral-400 italic">
            </div>

            <form wire:submit.prevent="submit"
                class="p-3 border-t border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 flex items-center gap-2">
                <input wire:model.live="newMessage" wire:keydown.debounce.300ms="userTyping" type="text"
                    class="flex-1 border border-neutral-300 dark:border-neutral-600 bg-transparent text-neutral-900 dark:text-white rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Type a message..." />

                <button type="submit" @if(empty($newMessage)) disabled @endif wire:loading.attr="disabled"
                    wire:target="submit"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm px-4 py-2 rounded-full transition">
                    Send
                </button>
            </form>

        @else
            <div
                class="flex-1 flex flex-col items-center justify-center text-neutral-400 bg-neutral-50/50 dark:bg-neutral-900/20 relative">
                <div class="absolute top-4 left-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg hover:bg-neutral-200 dark:hover:bg-neutral-700 text-neutral-600 dark:text-neutral-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
                <p class="text-lg">Select a user to start chatting.</p>
            </div>
        @endif

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
        console.log('Pokušavam pretplatu na kanal:', channelName);

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