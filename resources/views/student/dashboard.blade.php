@extends('layouts.student')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-8">

    {{-- Welcome --}}
    <div class="bg-gradient-to-br from-indigo-900/40 to-purple-900/30 rounded-2xl border border-indigo-800/50 p-6">
        <p class="text-indigo-300 text-sm font-semibold uppercase tracking-widest mb-1">Selamat datang,</p>
        <h1 class="text-2xl font-bold text-white">{{ auth()->user()->name }}</h1>
        <p class="text-gray-400 text-sm mt-1">{{ auth()->user()->class?->name ?? 'Kelas belum ditentukan' }}</p>
    </div>

    {{-- Available Quizzes --}}
    <section>
        <h2 class="text-lg font-bold text-white mb-4">📋 Quiz Tersedia</h2>
        @forelse($quizzes as $quiz)
            <a href="{{ route('student.quiz.take', $quiz) }}"
               class="flex items-center justify-between bg-gray-900 hover:bg-gray-800 border border-gray-800 hover:border-indigo-500/50 rounded-2xl p-5 mb-3 transition group">
                <div>
                    <p class="text-xs text-indigo-400 font-semibold uppercase tracking-widest mb-1">
                        {{ $quiz->subject->name }} &middot; {{ $quiz->type === 'mid_term' ? 'UTS' : 'UAS' }}
                    </p>
                    <p class="text-white font-semibold text-base group-hover:text-indigo-300 transition">{{ $quiz->title }}</p>
                    <p class="text-gray-500 text-sm mt-1">
                        {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' menit' : 'Tanpa batas waktu' }}
                        &middot; {{ $quiz->total_points }} poin
                    </p>
                </div>
                <span class="flex-shrink-0 ml-4 text-indigo-400 group-hover:translate-x-1 transition">→</span>
            </a>
        @empty
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 text-center text-gray-500">
                Tidak ada quiz yang tersedia saat ini.
            </div>
        @endforelse
    </section>

    {{-- Completed Quizzes --}}
    @if($completedAttempts->count())
    <section>
        <h2 class="text-lg font-bold text-white mb-4">✅ Riwayat Quiz</h2>
        @foreach($completedAttempts as $attempt)
            <a href="{{ route('student.quiz.stats', $attempt) }}"
               class="flex items-center justify-between bg-gray-900/60 border border-gray-800 rounded-2xl p-5 mb-3 hover:bg-gray-900 transition hover:border-indigo-500/50 group">
                <div>
                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">{{ $attempt->quiz->subject->name }}</p>
                    <p class="text-white font-medium">{{ $attempt->quiz->title }}</p>
                    <p class="text-gray-500 text-sm mt-0.5">{{ $attempt->submitted_at?->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold {{ $attempt->score >= 70 ? 'text-green-400' : 'text-red-400' }}">
                        {{ number_format($attempt->score, 0) }}
                    </p>
                    <p class="text-xs text-gray-500">/ {{ $attempt->quiz->total_points }}</p>
                </div>
            </a>
        @endforeach
    </section>
    @endif

    <livewire:join-class />
</div>
@endsection
