<?php

namespace App\Livewire;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\StudentAnswer;
use App\Services\GradingService;
use Illuminate\Support\Collection;
use Livewire\Component;

class QuizRunner extends Component
{
    public QuizAttempt $attempt;
    public Quiz $quiz;
    public Collection $questions;
    public int $currentIndex = 0;
    public array $answers = [];
    public bool $isSubmitted = false;
    public ?int $remainingSeconds = null;

    public function mount(Quiz $quiz): void
    {
        // Prevent students without a class or access
        abort_unless(auth()->check(), 403);

        // Prevent re-taking if already completed
        $completed = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', auth()->id())
            ->where('is_completed', true)
            ->first();

        if ($completed) {
            $this->redirect(route('student.quiz.result', $completed));
            return;
        }

        // Resume existing incomplete attempt OR create new
        $existing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', auth()->id())
            ->where('is_completed', false)
            ->first();

        $this->attempt = $existing ?? QuizAttempt::create([
            'quiz_id'    => $quiz->id,
            'student_id' => auth()->id(),
        ]);

        $this->quiz      = $quiz;
        $this->questions = $quiz->questions()->orderByPivot('order')->get();

        // Pre-fill answers from database (resume support)
        foreach ($this->attempt->answers as $answer) {
            $this->answers[$answer->question_id] = $answer->selected_option_id
                ?? $answer->short_answer_text;
        }

        // Init timer if quiz has duration
        if ($quiz->duration_minutes) {
            $elapsed = now()->diffInSeconds($this->attempt->started_at);
            $this->remainingSeconds = max(0, ($quiz->duration_minutes * 60) - $elapsed);
        }
    }

    // Called every 1s via wire:poll if timer is active
    public function tickTimer(): void
    {
        if ($this->remainingSeconds <= 0) {
            $this->autoSubmit();
            return;
        }
        // Always recalculate from server timestamp (anti-drift)
        $elapsed = now()->diffInSeconds($this->attempt->started_at);
        $this->remainingSeconds = max(0, ($this->quiz->duration_minutes * 60) - $elapsed);
    }

    public function saveAnswer(int $questionId, mixed $value): void
    {
        $this->answers[$questionId] = $value;

        $question = $this->questions->find($questionId);

        $payload = ['answered_at' => now()];

        if ($question && $question->type === 'mcq') {
            $payload['selected_option_id'] = $value;
            $payload['short_answer_text']  = null;
        } else {
            $payload['short_answer_text']  = $value;
            $payload['selected_option_id'] = null;
        }

        StudentAnswer::updateOrCreate(
            ['attempt_id' => $this->attempt->id, 'question_id' => $questionId],
            $payload
        );
    }

    public function next(): void
    {
        if ($this->currentIndex < $this->questions->count() - 1) {
            $this->currentIndex++;
        }
    }

    public function previous(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function jumpTo(int $index): void
    {
        $this->currentIndex = $index;
    }

    public function autoSubmit(): void
    {
        $this->submit();
    }

    public function submit(): void
    {
        if ($this->isSubmitted) {
            return;
        }

        GradingService::gradeAttempt($this->attempt);
        $this->isSubmitted = true;
        $this->redirect(route('student.quiz.result', $this->attempt));
    }

    public function render()
    {
        return view('livewire.quiz-runner')->layout('layouts.student');
    }
}
