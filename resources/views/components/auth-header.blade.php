@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h2 class="text-xl font-bold text-[#C50303]">{{ $title }}</h2>
    <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
</div>
