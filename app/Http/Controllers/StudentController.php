<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function dashboard(): View
    {
        $user = auth()->user();

        $quizzes = Quiz::whereHas('classes', fn ($q) =>
                $q->where('class_id', $user->class_id)
            )
            ->where('is_published', true)
            ->whereDoesntHave('attempts', fn ($q) =>
                $q->where('student_id', $user->id)->where('is_completed', true)
            )
            ->with('subject')
            ->get();

        $completedAttempts = QuizAttempt::where('student_id', $user->id)
            ->where('is_completed', true)
            ->with('quiz.subject')
            ->latest('submitted_at')
            ->get();

        return view('student.dashboard', compact('quizzes', 'completedAttempts'));
    }

    public function result(QuizAttempt $attempt): View
    {
        abort_unless($attempt->student_id === auth()->id(), 403);

        $attempt->load('quiz.subject', 'answers.question.options', 'answers.selectedOption');

        return view('student.result', compact('attempt'));
    }

    public function stats(QuizAttempt $attempt): View
    {
        abort_unless($attempt->student_id === auth()->id(), 403);
        abort_unless($attempt->is_completed, 404);

        $analysis = \App\Services\ChapterAnalysisService::analyse($attempt);

        return view('student.quiz.stats', [
            'attempt'  => $attempt->load('quiz.subject'),
            'analysis' => $analysis,
        ]);
    }
}
