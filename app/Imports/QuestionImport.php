<?php

namespace App\Imports;

use App\Models\McqOption;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionImport implements ToCollection, WithHeadingRow
{
    protected Quiz $quiz;

    public function __construct(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function collection(Collection $rows)
    {
        // Get the current max order to append questions
        $maxOrder = $this->quiz->questions()->max('order') ?? 0;

        foreach ($rows as $row) {
            // Validate required fields explicitly based on headings
            // Headings are automatically snake_cased by default
            if (empty($row['question']) || empty($row['correct_answer'])) {
                continue; // Skip invalid rows
            }

            $correctAnswer = strtoupper(trim($row['correct_answer']));
            if (!in_array($correctAnswer, ['A', 'B', 'C', 'D'])) {
                continue; // skip if invalid correct answer
            }

            // Create the Question
            $question = Question::create([
                'body' => $row['question'],
                'type' => 'mcq',
                'created_by' => auth()->id() ?? $this->quiz->teacher_id,
                'subject_id' => $this->quiz->subject_id
            ]);

            // Create MCQ Options
            $options = [
                'A' => $row['option_a'],
                'B' => $row['option_b'],
                'C' => $row['option_c'],
                'D' => $row['option_d'],
            ];

            foreach ($options as $label => $body) {
                if (!empty($body)) {
                    McqOption::create([
                        'question_id' => $question->id,
                        'label' => $label,
                        'body' => $body,
                        'is_correct' => ($label === $correctAnswer)
                    ]);
                }
            }

            // Attach to Quiz with correct order
            $maxOrder++;
            $this->quiz->questions()->attach($question->id, ['order' => $maxOrder]);
        }
    }
}
