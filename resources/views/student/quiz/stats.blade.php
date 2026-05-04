@extends('layouts.student')

@section('title', 'Statistik Quiz')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-2">
        <a href="{{ route('student.dashboard') }}" class="text-gray-500 hover:text-[#C50303] transition flex items-center gap-2 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Dashboard
        </a>
        <a href="{{ route('student.quiz.result', $attempt) }}" class="text-[#C50303] hover:text-[#a50202] transition text-sm font-semibold flex items-center gap-2">
            Lihat Pembahasan
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Score Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Score Card --}}
        <div class="md:col-span-1 bg-[#C50303] rounded-2xl p-6 text-center flex flex-col justify-center shadow-sm">
            <p class="text-red-200 text-xs font-bold uppercase tracking-widest mb-2">Skor Akhir</p>
            <h2 class="text-5xl font-black text-white mb-1">{{ number_format($attempt->score, 0) }}</h2>
            <p class="text-red-200 text-xs italic">Nilai Tertinggi: {{ $attempt->quiz->total_points }}</p>
        </div>

        {{-- Stats Grid --}}
        <div class="md:col-span-3 bg-white border border-gray-200 rounded-2xl p-6 grid grid-cols-3 gap-4 shadow-sm">
            <div class="flex flex-col items-center justify-center border-r border-gray-200">
                <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-1">Total Soal</p>
                <p class="text-2xl font-bold text-gray-900">{{ $analysis['summary']['total'] }}</p>
            </div>
            <div class="flex flex-col items-center justify-center border-r border-gray-200">
                <p class="text-green-600 text-xs font-semibold uppercase tracking-wider mb-1">Benar</p>
                <p class="text-2xl font-bold text-green-600">{{ $analysis['summary']['correct'] }}</p>
            </div>
            <div class="flex flex-col items-center justify-center">
                <p class="text-[#C50303] text-xs font-semibold uppercase tracking-wider mb-1">Salah</p>
                <p class="text-2xl font-bold text-[#C50303]">{{ $analysis['summary']['wrong'] }}</p>
            </div>
        </div>
    </div>

    {{-- Gray separator --}}
    <div class="h-px bg-gray-200"></div>

    {{-- Highlights --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($analysis['best_chapter'])
        <div class="bg-white border border-green-200 rounded-2xl p-6 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-green-600 text-xs font-bold uppercase tracking-widest mb-1">Materi Terkuat</p>
                <h3 class="text-gray-900 font-bold text-lg leading-tight">{{ $analysis['best_chapter']['chapter'] }}</h3>
                <p class="text-gray-400 text-sm mt-1">{{ $analysis['best_chapter']['correct_pct'] }}% Akurasi</p>
            </div>
        </div>
        @endif

        @if($analysis['focus_chapter'] && (!$analysis['best_chapter'] || $analysis['focus_chapter']['chapter'] !== $analysis['best_chapter']['chapter']))
        <div class="bg-white border border-orange-200 rounded-2xl p-6 flex items-center gap-5 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-500 flex-shrink-0">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <p class="text-orange-500 text-xs font-bold uppercase tracking-widest mb-1">Perlu Fokus</p>
                <h3 class="text-gray-900 font-bold text-lg leading-tight">{{ $analysis['focus_chapter']['chapter'] }}</h3>
                <p class="text-gray-400 text-sm mt-1">Hanya {{ $analysis['focus_chapter']['correct_pct'] }}% Akurasi</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Gray separator --}}
    <div class="h-px bg-gray-200"></div>

    {{-- Table Breakdown --}}
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-gray-800 font-bold text-base">Analisis Per Materi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-400 text-xs font-bold uppercase tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Materi (Chapter)</th>
                        <th class="px-6 py-4 text-center">Soal</th>
                        <th class="px-6 py-4 text-center">Benar</th>
                        <th class="px-6 py-4 text-center text-[#C50303]">Salah</th>
                        <th class="px-6 py-4 text-right">Akurasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($analysis['chapters'] as $data)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <span class="text-gray-800 font-medium text-sm">{{ $data['chapter'] }}</span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-500 text-sm">{{ $data['total'] }}</td>
                        <td class="px-6 py-4 text-center text-green-600 text-sm font-semibold">{{ $data['correct'] }}</td>
                        <td class="px-6 py-4 text-center text-[#C50303] text-sm font-semibold">{{ $data['wrong'] }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <div class="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $data['correct_pct'] >= 75 ? 'bg-green-500' : ($data['correct_pct'] >= 50 ? 'bg-yellow-500' : 'bg-[#C50303]') }}"
                                         style="width: {{ $data['correct_pct'] }}%"></div>
                                </div>
                                <span class="text-gray-800 font-bold text-sm min-w-[40px]">{{ $data['correct_pct'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
