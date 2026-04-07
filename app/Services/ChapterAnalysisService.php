<?php

namespace App\Services;

use App\Models\QuizAttempt;

class ChapterAnalysisService
{
    /**
     * Analyse chapter performance for a single completed quiz attempt.
     *
     * @param  QuizAttempt  $attempt
     * @return array{
     *     summary: array{ total: int, correct: int, wrong: int, correct_pct: float },
     *     chapters: array<int, array{
     *         chapter: string,
     *         total: int,
     *         correct: int,
     *         wrong: int,
     *         correct_pct: float,
     *         is_uncategorized: bool
     *     }>,
     *     best_chapter: array|null,
     *     focus_chapter: array|null,
     * }
     */
    public static function analyse(QuizAttempt $attempt): array
    {
        // Load all answers with their related question (including chapter relationship)
        $answers = $attempt->answers()
            ->with(['question.chapter'])
            ->get();

        // ----------------------------------------------------------------
        // Step 1: Build per-chapter buckets
        // ----------------------------------------------------------------
        $buckets = [];

        foreach ($answers as $answer) {
            // Use the chapter order/name from the relationship, or "Uncategorized"
            $chapterModel = $answer->question->chapter;
            $chapterName  = $chapterModel ? ($chapterModel->order . '. ' . $chapterModel->name) : 'Uncategorized';
            $chapterKey   = $chapterModel ? $chapterModel->id : 'null';

            if (!isset($buckets[$chapterKey])) {
                $buckets[$chapterKey] = [
                    'chapter'          => $chapterName,
                    'total'            => 0,
                    'correct'          => 0,
                    'wrong'            => 0,
                    'correct_pct'      => 0.0,
                    'is_uncategorized' => $chapterKey === 'null',
                ];
            }

            $buckets[$chapterKey]['total']++;

            if ($answer->is_correct) {
                $buckets[$chapterKey]['correct']++;
            } else {
                $buckets[$chapterKey]['wrong']++;
            }
        }

        // ----------------------------------------------------------------
        // Step 2: Calculate correct_pct for each chapter
        // ----------------------------------------------------------------
        foreach ($buckets as $key => $data) {
            $buckets[$key]['correct_pct'] = $data['total'] > 0
                ? round(($data['correct'] / $data['total']) * 100, 1)
                : 0.0;
        }

        // ----------------------------------------------------------------
        // Step 3: Sort chapters
        // Rule: natural order of titles, "Uncategorized" always last
        // ----------------------------------------------------------------
        uasort($buckets, function (array $a, array $b) {
            // Uncategorized always goes to the bottom
            if ($a['is_uncategorized'] && !$b['is_uncategorized']) return 1;
            if (!$a['is_uncategorized'] && $b['is_uncategorized']) return -1;

            // Natural sort (e.g. "2." before "10.")
            return strnatcasecmp($a['chapter'], $b['chapter']);
        });

        $chapters = array_values($buckets);

        // ----------------------------------------------------------------
        // Step 4: Find best chapter
        // Rule: highest correct_pct. Tie → most total questions.
        // ----------------------------------------------------------------
        $bestChapter = null;

        foreach ($chapters as $data) {
            if ($bestChapter === null) {
                $bestChapter = $data;
                continue;
            }

            $higherPct   = $data['correct_pct'] > $bestChapter['correct_pct'];
            $samePctMore = $data['correct_pct'] === $bestChapter['correct_pct']
                        && $data['total'] > $bestChapter['total'];

            if ($higherPct || $samePctMore) {
                $bestChapter = $data;
            }
        }

        // ----------------------------------------------------------------
        // Step 5: Find focus chapter
        // Rule: lowest correct_pct. Tie → most wrong answers (raw count).
        // ----------------------------------------------------------------
        $focusChapter = null;

        foreach ($chapters as $data) {
            if ($focusChapter === null) {
                $focusChapter = $data;
                continue;
            }

            $lowerPct      = $data['correct_pct'] < $focusChapter['correct_pct'];
            $samePctMore   = $data['correct_pct'] === $focusChapter['correct_pct']
                          && $data['wrong'] > $focusChapter['wrong'];

            if ($lowerPct || $samePctMore) {
                $focusChapter = $data;
            }
        }

        // ----------------------------------------------------------------
        // Step 6: Overall summary for this attempt
        // ----------------------------------------------------------------
        $totalCorrect = $answers->where('is_correct', true)->count();
        $totalWrong   = $answers->where('is_correct', false)->count();
        $totalAll     = $answers->count();

        $summary = [
            'total'       => $totalAll,
            'correct'     => $totalCorrect,
            'wrong'       => $totalWrong,
            'correct_pct' => $totalAll > 0
                ? round(($totalCorrect / $totalAll) * 100, 1)
                : 0.0,
        ];

        return [
            'summary'       => $summary,
            'chapters'      => $chapters,
            'best_chapter'  => $bestChapter,
            'focus_chapter' => $focusChapter,
        ];
    }
}
