<?php

namespace App\Services;

use App\Models\QuizAttempt;
use App\Models\StudentAnswer;

class GradingService
{
    public static function gradeAttempt(QuizAttempt $attempt): void
    {
        $quiz = $attempt->quiz()->with('questions.options')->first();
        $questionCount = $quiz->questions->count();

        if ($questionCount === 0) {
            $attempt->update([
                'score'        => 0,
                'is_completed' => true,
                'submitted_at' => now(),
            ]);
            return;
        }

        $pointsPerQuestion = $quiz->total_points / $questionCount;
        $totalEarned = 0;

        foreach ($attempt->answers as $answer) {
            $question  = $quiz->questions->find($answer->question_id);
            $isCorrect = false;

            if (! $question) {
                continue;
            }

            if ($question->type === 'mcq') {
                $isCorrect = optional($answer->selectedOption)->is_correct ?? false;

            } elseif ($question->type === 'short_answer') {
                $keywords  = $question->keywords ?? [];
                $threshold = $question->keyword_threshold ?? 1;
                $text      = strtolower($answer->short_answer_text ?? '');

                $matched = collect($keywords)
                    ->filter(fn ($kw) => str_contains($text, strtolower($kw)))
                    ->count();

                $isCorrect = $matched >= $threshold;
            }

            $earned       = $isCorrect ? $pointsPerQuestion : 0;
            $totalEarned += $earned;

            $answer->update([
                'is_correct'    => $isCorrect,
                'points_earned' => $earned,
            ]);
        }

        $attempt->update([
            'score'        => $totalEarned,
            'is_completed' => true,
            'submitted_at' => now(),
        ]);
    }
}
