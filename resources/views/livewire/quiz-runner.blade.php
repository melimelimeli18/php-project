<div class="space-y-6" oncontextmenu="return false;" oncopy="return false;" oncut="return false;" onpaste="return false;">

    {{-- Header: Quiz Title + Timer --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 flex items-start justify-between gap-4 shadow-sm">
        <div>
            <p class="text-xs uppercase tracking-widest text-[#C50303] font-semibold mb-1">
                {{ $quiz->subject->name }} &middot; {{ $quiz->type === 'mid_term' ? 'UTS' : 'UAS' }}
            </p>
            <h1 class="text-2xl font-bold text-gray-900">{{ $quiz->title }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $questions->count() }} soal &middot; Total {{ $quiz->total_points }} poin</p>
        </div>

        {{-- Timer --}}
        @if($remainingSeconds !== null)
            <div
                wire:poll.1000ms="tickTimer"
                class="flex-shrink-0 text-center bg-white rounded-xl px-5 py-3 border {{ $remainingSeconds < 60 ? 'border-[#C50303] animate-pulse' : 'border-gray-300' }} shadow-sm"
            >
                <p class="text-xs text-gray-400 mb-1">Sisa Waktu</p>
                <p class="text-2xl font-mono font-bold {{ $remainingSeconds < 60 ? 'text-[#C50303]' : 'text-gray-800' }}">
                    {{ gmdate('i:s', $remainingSeconds) }}
                </p>
            </div>
        @endif
    </div>

    {{-- Progress bar --}}
    <div class="w-full bg-gray-200 rounded-full h-1.5">
        <div
            class="bg-[#C50303] h-1.5 rounded-full transition-all duration-300"
            style="width: {{ (($currentIndex + 1) / $questions->count()) * 100 }}%"
        ></div>
    </div>

    {{-- Question Navigator --}}
    <div class="flex flex-wrap gap-2">
        @foreach($questions as $i => $q)
            <button
                wire:click="jumpTo({{ $i }})"
                id="nav-q-{{ $i }}"
                class="w-9 h-9 rounded-lg text-sm font-semibold transition
                    {{ $i === $currentIndex
                        ? 'bg-[#C50303] text-white shadow-md'
                        : (isset($answers[$q->id])
                            ? 'bg-green-100 text-green-700 border border-green-400'
                            : 'bg-white text-gray-500 border border-gray-200 hover:border-[#C50303] hover:text-[#C50303]') }}"
            >
                {{ $i + 1 }}
            </button>
        @endforeach
    </div>

    {{-- Current Question --}}
    @php $question = $questions[$currentIndex]; @endphp

    <div class="bg-white border border-gray-200 rounded-2xl p-6 space-y-5 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-[#C50303] text-sm font-bold ring-1 ring-red-300">
                {{ $currentIndex + 1 }}
            </span>
            <span class="text-xs uppercase tracking-widest text-gray-400">
                {{ $question->type === 'mcq' ? 'Pilihan Ganda' : 'Jawaban Singkat' }}
            </span>
        </div>

        <p class="text-lg text-gray-800 leading-relaxed">{{ $question->body }}</p>

        {{-- MCQ Options --}}
        @if($question->type === 'mcq')
            <div class="space-y-3">
                @foreach($question->options as $option)
                    <label
                        id="option-{{ $option->id }}"
                        class="flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition
                            {{ ($answers[$question->id] ?? null) == $option->id
                                ? 'bg-red-50 border-[#C50303] text-gray-900 shadow-sm'
                                : 'bg-gray-50 border-gray-200 text-gray-700 hover:border-[#C50303] hover:bg-red-50/40' }}"
                    >
                        <input
                            type="radio"
                            name="q{{ $question->id }}"
                            value="{{ $option->id }}"
                            class="hidden"
                            wire:click="saveAnswer({{ $question->id }}, {{ $option->id }})"
                            {{ ($answers[$question->id] ?? null) == $option->id ? 'checked' : '' }}
                        />
                        <span class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center
                            {{ ($answers[$question->id] ?? null) == $option->id ? 'bg-[#C50303] text-white' : 'bg-gray-200 text-gray-600' }}
                            font-bold text-sm">
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
                class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#C50303] focus:border-[#C50303] transition resize-none"
            >{{ $answers[$question->id] ?? '' }}</textarea>
        @endif
    </div>

    {{-- Navigation Buttons --}}
    <div class="flex justify-between items-center gap-4">
        <button
            wire:click="previous"
            @if($currentIndex === 0) disabled @endif
            id="btn-previous"
            class="px-5 py-2.5 rounded-xl bg-white border border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed transition font-medium shadow-sm"
        >
            ← Sebelumnya
        </button>

        @if($currentIndex < $questions->count() - 1)
            <button
                wire:click="next"
                id="btn-next"
                class="px-5 py-2.5 rounded-xl bg-[#C50303] text-white hover:bg-[#a50202] transition font-medium shadow-sm"
            >
                Selanjutnya →
            </button>
        @else
            <button
                wire:click="submit"
                wire:confirm="Apakah kamu yakin ingin mengumpulkan jawaban? Kamu tidak bisa mengubah jawaban setelah submit."
                id="btn-submit"
                class="px-6 py-2.5 rounded-xl bg-green-600 text-white hover:bg-green-500 transition font-bold shadow-sm"
            >
                Kumpulkan ✓
            </button>
        @endif
    </div>

    @script
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && (e.key === 'c' || e.key === 'v' || e.key === 'x' || e.key === 'C' || e.key === 'V' || e.key === 'X')) {
                e.preventDefault();
            }
        });
    </script>
    @endscript

</div>
