<div class="space-y-6">

    {{-- Header: Quiz Title + Timer --}}
    <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6 flex items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-widest text-indigo-400 font-semibold mb-1">
                {{ $quiz->subject->name }} &middot; {{ $quiz->type === 'mid_term' ? 'UTS' : 'UAS' }}
            </p>
            <h1 class="text-2xl font-bold text-white">{{ $quiz->title }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $questions->count() }} soal &middot; Total {{ $quiz->total_points }} poin</p>
        </div>

        {{-- Timer --}}
        @if($remainingSeconds !== null)
            <div
                wire:poll.1000ms="tickTimer"
                class="flex-shrink-0 text-center bg-gray-800 rounded-xl px-5 py-3 border {{ $remainingSeconds < 60 ? 'border-red-500 animate-pulse' : 'border-gray-700' }}"
            >
                <p class="text-xs text-gray-400 mb-1">Sisa Waktu</p>
                <p class="text-2xl font-mono font-bold {{ $remainingSeconds < 60 ? 'text-red-400' : 'text-white' }}">
                    {{ gmdate('i:s', $remainingSeconds) }}
                </p>
            </div>
        @endif
    </div>

    {{-- Question Navigator --}}
    <div class="flex flex-wrap gap-2">
        @foreach($questions as $i => $q)
            <button
                wire:click="jumpTo({{ $i }})"
                id="nav-q-{{ $i }}"
                class="w-9 h-9 rounded-lg text-sm font-semibold transition
                    {{ $i === $currentIndex
                        ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30'
                        : (isset($answers[$q->id])
                            ? 'bg-green-700/40 text-green-300 border border-green-600'
                            : 'bg-gray-800 text-gray-400 hover:bg-gray-700') }}"
            >
                {{ $i + 1 }}
            </button>
        @endforeach
    </div>

    {{-- Current Question --}}
    @php $question = $questions[$currentIndex]; @endphp

    <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6 space-y-5">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600/20 text-indigo-400 text-sm font-bold ring-1 ring-indigo-500/40">
                {{ $currentIndex + 1 }}
            </span>
            <span class="text-xs uppercase tracking-widest text-gray-500">
                {{ $question->type === 'mcq' ? 'Pilihan Ganda' : 'Jawaban Singkat' }}
            </span>
        </div>

        <p class="text-lg text-white leading-relaxed">{{ $question->body }}</p>

        {{-- MCQ Options --}}
        @if($question->type === 'mcq')
            <div class="space-y-3">
                @foreach($question->options as $option)
                    <label
                        id="option-{{ $option->id }}"
                        class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition
                            {{ ($answers[$question->id] ?? null) == $option->id
                                ? 'bg-indigo-600/20 border-indigo-500 text-white'
                                : 'bg-gray-800 border-gray-700 text-gray-300 hover:border-indigo-500/50' }}"
                    >
                        <input
                            type="radio"
                            name="q{{ $question->id }}"
                            value="{{ $option->id }}"
                            class="hidden"
                            wire:click="saveAnswer({{ $question->id }}, {{ $option->id }})"
                            {{ ($answers[$question->id] ?? null) == $option->id ? 'checked' : '' }}
                        />
                        <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center bg-gray-700 text-indigo-300 font-bold text-sm">
                            {{ $option->label }}
                        </span>
                        <span>{{ $option->body }}</span>
                    </label>
                @endforeach
            </div>

        {{-- Short Answer --}}
        @elseif($question->type === 'short_answer')
            <textarea
                id="short-answer-{{ $question->id }}"
                rows="4"
                wire:model.lazy="answers.{{ $question->id }}"
                wire:change="saveAnswer({{ $question->id }}, $event.target.value)"
                placeholder="Tulis jawaban kamu di sini..."
                class="w-full bg-gray-800 border border-gray-700 rounded-xl p-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition resize-none"
            >{{ $answers[$question->id] ?? '' }}</textarea>
        @endif
    </div>

    {{-- Navigation Buttons --}}
    <div class="flex justify-between items-center gap-4">
        <button
            wire:click="previous"
            @if($currentIndex === 0) disabled @endif
            id="btn-previous"
            class="px-5 py-2.5 rounded-xl bg-gray-800 text-gray-300 hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed transition font-medium"
        >
            ← Sebelumnya
        </button>

        @if($currentIndex < $questions->count() - 1)
            <button
                wire:click="next"
                id="btn-next"
                class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-500 transition font-medium shadow-lg shadow-indigo-500/20"
            >
                Selanjutnya →
            </button>
        @else
            <button
                wire:click="submit"
                wire:confirm="Apakah kamu yakin ingin mengumpulkan jawaban? Kamu tidak bisa mengubah jawaban setelah submit."
                id="btn-submit"
                class="px-6 py-2.5 rounded-xl bg-green-600 text-white hover:bg-green-500 transition font-bold shadow-lg shadow-green-500/20"
            >
                Kumpulkan ✓
            </button>
        @endif
    </div>

    {{-- Progress bar --}}
    <div class="w-full bg-gray-800 rounded-full h-1.5">
        <div
            class="bg-indigo-500 h-1.5 rounded-full transition-all duration-300"
            style="width: {{ (($currentIndex + 1) / $questions->count()) * 100 }}%"
        ></div>
    </div>

</div>
