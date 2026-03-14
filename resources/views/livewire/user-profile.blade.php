<div class="flex h-full w-full flex-1 flex-col gap-6 p-4">

    {{-- PROFILE HEADER --}}
    <div class="max-w-2xl mx-auto w-full">
        <div
            class="flex flex-col items-center justify-center p-8 bg-white dark:bg-neutral-800 rounded-3xl border border-neutral-200 dark:border-neutral-700 shadow-sm relative overflow-hidden">
            <div class="relative">
                @php
                    /** * On Railway, profile_image is no longer just a filename.
                     * It stores the full Cloudinary URL (e.g., https://res.cloudinary.com/...)
                     */
                    $profilePhotoUrl = $user->profile_image;
                @endphp

                @if ($profilePhotoUrl)
                    {{-- Display the Cloudinary URL directly --}}
                    <img src="{{ $profilePhotoUrl }}"
                        class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-neutral-800 shadow-xl">
                @else
                    {{-- Fallback if the user has no profile photo --}}
                    <div
                        class="w-32 h-32 bg-blue-600 rounded-full flex items-center justify-center text-4xl font-bold text-white shadow-xl">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <h1 class="mt-4 text-3xl font-extrabold text-neutral-900 dark:text-white">
                {{ $user->name }}
            </h1>

            <p class="text-sm text-neutral-500 italic">Joined {{ $user->created_at->format('F Y') }}</p>

            <div class="flex items-center justify-center mt-2 space-x-2 text-sm text-neutral-500">
                <span>{{ $posts->count() }} Posts</span>
                <span class="text-neutral-300">&middot;</span>
                <span class="text-red-500 font-bold">{{ $this->likesCount }} Total Likes</span>
            </div>

            @if($isMyProfile)
                <flux:button wire:click="openCreateModal" variant="primary" class="mt-6 rounded-full font-bold shadow-lg">
                    + Create New Post
                </flux:button>

            @elseif($isFriend)
                <flux:modal.trigger name="confirm-unfriend">
                    <button type="button"
                        class="group mt-6 flex items-center gap-2 bg-neutral-100 dark:bg-neutral-700 text-neutral-600 dark:text-neutral-300 px-6 py-2 rounded-full font-bold border border-neutral-200 dark:border-neutral-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5 text-emerald-500 group-hover:hidden">
                            <path fill-rule="evenodd"
                                d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.74-5.24Z"
                                clip-rule="evenodd" />
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                            stroke="currentColor" class="w-5 h-5 hidden group-hover:block text-red-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                        <span class="group-hover:hidden">Friends</span>
                        <span class="hidden group-hover:block">Unfriend</span>
                    </button>
                </flux:modal.trigger>

                <flux:modal name="confirm-unfriend" class="min-w-[22rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">Unfriend {{ $user->name }}?</flux:heading>
                            <flux:subheading>Are you sure you want to remove this person from your friends list?
                            </flux:subheading>
                        </div>

                        <div class="flex gap-2">
                            <flux:spacer />

                            <flux:modal.close>
                                <flux:button variant="ghost">Cancel</flux:button>
                            </flux:modal.close>

                            <flux:button wire:click="removeFriend({{ $user->id }})"
                                x-on:click="Flux.modal('confirm-unfriend').close()" variant="danger">
                                Unfriend
                            </flux:button>
                        </div>
                    </div>
                </flux:modal>

            @else
                <flux:button wire:click="addFriend({{ $user->id }})" variant="filled"
                    class="mt-6 bg-emerald-500 hover:bg-emerald-600 text-white px-8 rounded-full font-bold shadow-lg">
                    Add Friend
                </flux:button>
            @endif
        </div>
    </div>

    {{-- POSTS FEED --}}
    <div class="max-w-2xl mx-auto w-full space-y-6 pb-20">
        <h3 class="text-lg font-bold dark:text-white">
            {{ $isMyProfile ? __('Your Timeline') : $user->name . __('\'s Timeline') }}
        </h3>

        @foreach ($posts as $post)
            <div wire:key="post-{{ $post->id }}"
                class="p-6 rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-sm">

                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs text-neutral-500">{{ $post->created_at->diffForHumans() }}</span>
                    @if($isMyProfile)
                        <div class="flex space-x-3">
                            <button wire:click="editPost({{ $post->id }})"
                                class="text-blue-600 text-sm font-bold hover:underline">Edit</button>

                            {{-- Zamena za stari Delete button --}}
                            <flux:modal.trigger name="delete-post-{{ $post->id }}">
                                <button class="text-red-500 text-sm font-bold hover:underline">Delete</button>
                            </flux:modal.trigger>

                            <flux:modal name="delete-post-{{ $post->id }}" class="min-w-[22rem]">
                                <div class="space-y-6">
                                    <div>
                                        <flux:heading size="lg">Delete Post?</flux:heading>
                                        <flux:subheading>Are you sure you want to delete this post?</flux:subheading>
                                    </div>

                                    <div class="flex gap-2">
                                        <flux:spacer />

                                        <flux:modal.close>
                                            <flux:button variant="ghost">Cancel</flux:button>
                                        </flux:modal.close>

                                        <flux:button wire:click="deletePost({{ $post->id }})" variant="danger">
                                            Delete Post
                                        </flux:button>
                                    </div>
                                </div>
                            </flux:modal>
                        </div>
                    @endif
                </div>

                <h2 class="text-2xl font-bold dark:text-white mb-2">{{ $post->title }}</h2>

                @if($post->image)
                    <div class="rounded-xl overflow-hidden border dark:border-neutral-700 my-4 bg-gray-50 dark:bg-neutral-900">
                        {{--
                        IMPORTANT: Post images are now loaded directly from the Cloudinary URL
                        stored in the database.
                        --}}
                        <img src="{{ $post->image }}" class="w-full h-auto max-h-[500px] object-contain mx-auto block">
                    </div>
                @endif

                <p class="text-neutral-700 dark:text-neutral-300 mb-4">{{ $post->content }}</p>

                {{-- LIKE SECTION --}}
                <div class="flex items-center pt-4 border-t border-neutral-100 dark:border-neutral-700">
                    @if($post->user_id === auth()->id())
                        {{-- Owner View (Static counter with tooltip) --}}
                        <div
                            class="flex items-center space-x-2 py-2 px-3 bg-neutral-50 dark:bg-neutral-900/50 rounded-full group relative cursor-help">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                class="w-5 h-5 text-red-500">
                                <path
                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            <span class="text-sm font-bold text-neutral-600 dark:text-neutral-400">
                                {{ $post->likes->count() }} {{ Str::plural('Like', $post->likes->count()) }}
                            </span>

                            @if($post->likes->count() > 0)
                                {{-- Tooltip: List of users who liked the post --}}
                                <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-50">
                                    <div
                                        class="bg-gray-900 text-white text-[10px] rounded-lg py-2 px-3 shadow-xl whitespace-nowrap">
                                        <p class="font-bold mb-1 border-b border-gray-700 pb-1">Liked by:</p>
                                        @foreach($post->likes->take(5) as $like)
                                            <div class="flex items-center gap-1">
                                                <span>• {{ $like->user->name }}</span>
                                            </div>
                                        @endforeach
                                        @if($post->likes->count() > 5)
                                            <p class="text-gray-400 italic">+ {{ $post->likes->count() - 5 }} more</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Interactive Like Button for other users --}}
                        <button wire:click="toggleLike({{ $post->id }})"
                            class="flex items-center space-x-2 {{ $post->isLikedBy(auth()->id()) ? 'text-red-500' : 'text-neutral-500' }} hover:scale-105 transition">
                            <flux:icon.heart variant="{{ $post->isLikedBy(auth()->id()) ? 'solid' : 'outline' }}"
                                class="w-6 h-6" />
                            <span class="font-bold">{{ $post->likes()->count() }}</span>
                        </button>
                    @endif
                </div>
            </div>
        @endforeach

        @if($posts->isEmpty())
            <div class="text-center py-10 text-neutral-500 italic">
                No posts yet. Start by creating one!
            </div>
        @endif
    </div>

    @include('livewire.create-post-modal')
</div>