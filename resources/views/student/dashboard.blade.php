@extends('layouts.student')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-8">

    {{-- Welcome Banner --}}
    <div class="bg-[#C50303] rounded-2xl p-6 text-white shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-widest text-red-200 mb-1">Selamat datang,</p>
        <h1 class="text-2xl font-bold">{{ auth()->user()->name }}</h1>
        <p class="text-red-200 text-sm mt-1">{{ auth()->user()->class?->name ?? 'Kelas belum ditentukan' }}</p>
    </div>

    {{-- Gray separator --}}
    <div class="h-px bg-gray-200"></div>

    {{-- Available Quizzes --}}
    <section>
        <h2 class="text-base font-bold text-gray-700 uppercase tracking-widest mb-4">📋 Quiz Tersedia</h2>
        @forelse($quizzes as $quiz)
            <a href="{{ route('student.quiz.take', $quiz) }}"
               class="flex items-center justify-between bg-white hover:bg-red-50 border border-gray-200 hover:border-[#C50303] rounded-2xl p-5 mb-3 transition group shadow-sm">
                <div>
                    <p class="text-xs text-[#C50303] font-semibold uppercase tracking-widest mb-1">
                        {{ $quiz->subject->name }} &middot; {{ $quiz->type === 'mid_term' ? 'UTS' : 'UAS' }}
                    </p>
                    <p class="text-gray-900 font-semibold text-base group-hover:text-[#C50303] transition">{{ $quiz->title }}</p>
                    <p class="text-gray-400 text-sm mt-1">
                        {{ $quiz->duration_minutes ? $quiz->duration_minutes . ' menit' : 'Tanpa batas waktu' }}
                        &middot; {{ $quiz->total_points }} poin
                    </p>
                </div>
                <span class="flex-shrink-0 ml-4 text-gray-300 group-hover:text-[#C50303] group-hover:translate-x-1 transition">→</span>
            </a>
        @empty
            <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center text-gray-400 shadow-sm">
                Tidak ada quiz yang tersedia saat ini.
            </div>
        @endforelse
    </section>

    {{-- Gray separator --}}
    @if($completedAttempts->count())
    <div class="h-px bg-gray-200"></div>
    @endif

    {{-- Completed Quizzes --}}
    @if($completedAttempts->count())
    <section>
        <h2 class="text-base font-bold text-gray-700 uppercase tracking-widest mb-4">✅ Riwayat Quiz</h2>
        @foreach($completedAttempts as $attempt)
            <a href="{{ route('student.quiz.stats', $attempt) }}"
               class="flex items-center justify-between bg-white border border-gray-200 rounded-2xl p-5 mb-3 hover:bg-gray-50 hover:border-gray-300 transition group shadow-sm">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">{{ $attempt->quiz->subject->name }}</p>
                    <p class="text-gray-900 font-medium">{{ $attempt->quiz->title }}</p>
                    <p class="text-gray-400 text-sm mt-0.5">{{ $attempt->submitted_at?->diffForHumans() }}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold {{ $attempt->score >= 70 ? 'text-green-600' : 'text-[#C50303]' }}">
                        {{ number_format($attempt->score, 0) }}
                    </p>
                    <p class="text-xs text-gray-400">/ {{ $attempt->quiz->total_points }}</p>
                </div>
            </a>
        @endforeach
    </section>
    @endif

    <livewire:join-class />
</div>
@endsection
