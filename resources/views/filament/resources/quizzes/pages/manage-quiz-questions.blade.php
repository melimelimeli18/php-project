<x-filament-panels::page>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold tracking-tight">Questions for: {{ $record->title }}</h2>
        {{-- Excel import action button goes here --}}
    </div>

    @livewire('quiz-question-manager', ['quiz' => $record])
    @livewire('assign-chapter-modal', ['quiz' => $record])
</x-filament-panels::page>
