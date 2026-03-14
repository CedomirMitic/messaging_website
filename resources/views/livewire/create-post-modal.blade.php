<div x-data="{ show: @entangle('showPostModal').live }" x-cloak>
    {{-- Backdrop --}}
    <div x-show="show" x-transition:opacity class="fixed inset-0 bg-black/50 z-40 transition-opacity duration-300">
    </div>

    {{-- Modal --}}
    <div x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
            <div class="relative bg-white dark:bg-neutral-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full modal-section">

                {{-- Form Header --}}
                <div class="px-4 py-3 sm:px-6 border-b dark:border-neutral-700">
                    <h3 class="text-lg leading-6 font-medium text-neutral-900 dark:text-white" id="modal-title">
                        {{ $editingPostId ? 'Edit Post' : 'Create New Post' }}
                    </h3>
                </div>

                <form wire:submit.prevent="{{ $editingPostId ? 'updatePost' : 'submitPost' }}">
                    <div class="p-6">
                        {{-- Title Input --}}
                        <input wire:model="{{ $editingPostId ? 'editTitle' : 'title' }}" type="text"
                            class="w-full border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-4 p-2"
                            placeholder="Post Title (Required)" />
                        @error($editingPostId ? 'editTitle' : 'title') 
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror

                        {{-- Image Input Section --}}
                        <div class="mb-4">
                            {{-- Separate inputs for editing vs creating to keep Livewire upload states clean --}}
                            @if($editingPostId)
                                <input type="file" wire:model.live="editImage"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            @else
                                <input type="file" wire:model.live="image"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            @endif

                            <div class="mt-4">
                                {{-- Loading indicator for file uploads --}}
                                <div wire:loading wire:target="editImage, image"
                                    class="text-xs text-blue-500 mb-2 italic">
                                    Uploading preview...
                                </div>

                                {{-- Image Preview Logic --}}
                                @if ($editImage)
                                    {{-- Case A: Newly uploaded image while EDITING --}}
                                    <div class="relative w-full h-48 rounded-xl overflow-hidden border-2 border-blue-500 bg-neutral-100 dark:bg-neutral-900">
                                        <img src="{{ $editImage->temporaryUrl() }}" class="w-full h-full object-contain">
                                        <button type="button" wire:click="$set('editImage', null)"
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 shadow-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @elseif ($image)
                                    {{-- Case B: Newly uploaded image for a NEW post --}}
                                    <div class="relative w-full h-48 rounded-xl overflow-hidden border-2 border-blue-500 bg-neutral-100 dark:bg-neutral-900">
                                        <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-contain">
                                        <button type="button" wire:click="$set('image', null)"
                                            class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1.5 shadow-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @elseif($editingPostId && ($existingPost = $posts->firstWhere('id', $editingPostId)) && $existingPost->image)
                                    {{-- Case C: Displaying existing Cloudinary image from DB --}}
                                    <div class="relative w-full h-48 rounded-xl overflow-hidden border dark:border-neutral-700 bg-neutral-100 dark:bg-neutral-900">
                                        {{-- Directly using the URL stored in database --}}
                                        <img src="{{ $existingPost->image }}"
                                            class="w-full h-full object-contain">
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Textarea Input --}}
                        <textarea wire:model="{{ $editingPostId ? 'editContent' : 'content' }}" rows="4"
                            class="w-full border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Share your thoughts..."></textarea>
                        @error($editingPostId ? 'editContent' : 'content') 
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:w-auto sm:text-sm"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updatePost, submitPost">
                                {{ $editingPostId ? 'Update Post' : 'Create Post' }}
                            </span>
                            <span wire:loading wire:target="updatePost, submitPost" class="flex items-center">
                                <svg class="animate-spin text-white h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>

                        <button type="button" wire:click="cancelEdit"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-neutral-600 dark:bg-neutral-700 dark:text-white shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 dark:hover:bg-neutral-600 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>