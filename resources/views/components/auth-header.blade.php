@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl" class="text-[#E9DCC9]">{{ $title }}</flux:heading>
    <flux:subheading class="text-zinc-400">{{ $description }}</flux:subheading>
</div>
