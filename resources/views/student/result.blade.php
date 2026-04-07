@extends('layouts.student')

@section('title', 'Hasil Quiz')

@section('content')
<div class="space-y-6">

    {{-- Score Card --}}
    <div class="bg-gradient-to-br from-gray-900 to-indigo-900/30 border border-gray-800 rounded-2xl p-8 text-center">
        <p class="text-sm uppercase tracking-widest text-indigo-400 font-semibold mb-2">
            {{ $attempt->quiz->subject->name }} &middot; {{ $attempt->quiz->title }}
        </p>
        <div class="text-7xl font-extrabold my-4 {{ $attempt->score >= 70 ? 'text-green-400' : 'text-red-400' }}">
            {{ number_format($attempt->score, 0) }}
        </div>
        <p class="text-gray-400 text-base">dari {{ $attempt->quiz->total_points }} poin</p>
        <p class="text-gray-500 text-sm mt-2">Dikumpulkan: {{ $attempt->submitted_at?->format('d M Y, H:i') }}</p>

        <div class="mt-6">
            <a href="{{ route('student.dashboard') }}"
               class="inline-block px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-semibold transition">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    {{-- Answer Review --}}
    <h2 class="text-lg font-bold text-white">Pembahasan Jawaban</h2>

    @foreach($attempt->answers as $i => $answer)
        @php $question = $answer->question; @endphp
        <div class="bg-gray-900 border {{ $answer->is_correct ? 'border-green-700' : 'border-red-800' }} rounded-2xl p-5 space-y-3">
            <div class="flex items-start gap-3">
                <span class="flex-shrink-0 w-7 h-7 rounded-full {{ $answer->is_correct ? 'bg-green-600/20 text-green-400' : 'bg-red-600/20 text-red-400' }} flex items-center justify-center text-sm font-bold">
                    {{ $i + 1 }}
                </span>
                <p class="text-white">{{ $question->body }}</p>
            </div>

            @if($question->type === 'mcq')
                <div class="pl-10 space-y-2">
                    @foreach($question->options as $option)
                        <div class="flex items-center gap-3 text-sm px-4 py-2 rounded-lg
                            {{ $option->is_correct ? 'bg-green-700/20 text-green-300 border border-green-700/50' : '' }}
                            {{ !$option->is_correct && $answer->selected_option_id === $option->id ? 'bg-red-700/20 text-red-300 border border-red-700/50' : '' }}
                        ">
                            <span class="font-bold w-5">{{ $option->label }}</span>
                            <span>{{ $option->body }}</span>
                            @if($option->is_correct) <span class="ml-auto text-green-400">✓ Benar</span> @endif
                            @if(!$option->is_correct && $answer->selected_option_id === $option->id) <span class="ml-auto text-red-400">✗ Jawabanmu</span> @endif
                        </div>
                    @endforeach
                </div>
            @elseif($question->type === 'short_answer')
                <div class="pl-10 space-y-2 text-sm">
                    <div class="bg-gray-800 rounded-lg p-3">
                        <p class="text-gray-400 text-xs mb-1">Jawabanmu:</p>
                        <p class="text-white">{{ $answer->short_answer_text ?: '—' }}</p>
                    </div>
                    @if($question->keywords)
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="text-gray-500 text-xs">Kata kunci:</span>
                        @foreach($question->keywords as $kw)
                            @php $found = str_contains(strtolower($answer->short_answer_text ?? ''), strtolower($kw)); @endphp
                            <span class="px-2 py-0.5 rounded text-xs {{ $found ? 'bg-green-700/30 text-green-300' : 'bg-gray-700 text-gray-400' }}">
                                {{ $kw }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            @endif

            <div class="pl-10 text-sm font-medium {{ $answer->is_correct ? 'text-green-400' : 'text-red-400' }}">
                +{{ number_format($answer->points_earned, 0) }} poin
            </div>
        </div>
    @endforeach

</div>
@endsection
