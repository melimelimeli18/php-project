<?php

namespace App\Livewire;

use App\Models\McqOption;
use App\Models\Question;
use App\Models\Quiz;
use Livewire\Component;

class QuizQuestionManager extends Component
{
    public Quiz $quiz;

    // Modal state
    public bool $isEditModalOpen = false;
    public ?int $editingQuestionId = null;
    public string $editBody = '';
    public array $editOptions = [];      // ['A' => ['id'=>…,'body'=>…], …]
    public string $editCorrectLabel = 'A';
    public ?int $editChapterId = null;   // NEW

    public array $subjectChapters = []; // NEW: cache for dropdown

    protected $listeners = ['questionsImported' => '$refresh'];

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz;
        // Load all chapters for this subject
        $this->subjectChapters = \App\Models\Chapter::where('subject_id', $quiz->subject_id)
            ->oldest('order')
            ->get(['id', 'order', 'name'])
            ->toArray();
    }

    public function getQuestionsProperty()
    {
        return $this->quiz->questions()->with(['options', 'chapter'])->get();
    }

    /** Drag-and-drop reorder */
    public function reorderQuestions($oldIndex, $newIndex)
    {
        $questions = $this->questions->pluck('id')->toArray();
        $movedItem = array_splice($questions, $oldIndex, 1)[0];
        array_splice($questions, $newIndex, 0, $movedItem);

        foreach ($questions as $index => $questionId) {
            $this->quiz->questions()->updateExistingPivot($questionId, ['order' => $index + 1]);
        }
    }

    /** Delete question from quiz */
    public function deleteQuestion($questionId)
    {
        $this->quiz->questions()->detach($questionId);
        Question::find($questionId)?->delete();
    }

    /** Open edit modal for a question */
    public function openEditModal($questionId)
    {
        $question = Question::with('options')->find($questionId);
        if (!$question) return;

        $this->editingQuestionId = $questionId;
        $this->editBody = $question->body;
        $this->editChapterId = $question->chapter_id; // NEW

        // Build keyed array ['A' => ['id'=>…,'body'=>…], …]
        $this->editOptions = $question->options
            ->sortBy('label')
            ->mapWithKeys(fn($o) => [$o->label => ['id' => $o->id, 'body' => $o->body]])
            ->toArray();

        $this->editCorrectLabel = $question->options->firstWhere('is_correct', true)?->label ?? 'A';
        $this->isEditModalOpen = true;
    }

    /** Save changes from modal */
    public function saveEdit()
    {
        $this->validate([
            'editBody'         => 'required|string',
            'editCorrectLabel' => 'required|in:A,B,C,D',
            'editChapterId'    => 'nullable|exists:chapters,id',
        ]);

        Question::where('id', $this->editingQuestionId)->update([
            'body'       => $this->editBody,
            'chapter_id' => $this->editChapterId, // NEW
        ]);

        foreach ($this->editOptions as $label => $data) {
            McqOption::where('id', $data['id'])->update([
                'body'       => $data['body'],
                'is_correct' => ($label === $this->editCorrectLabel),
            ]);
        }

        $this->closeEdit();
    }

    /** Close modal without saving */
    public function closeEdit()
    {
        $this->isEditModalOpen = false;
        $this->editingQuestionId = null;
        $this->editBody = '';
        $this->editChapterId = null; // NEW
        $this->editOptions = [];
        $this->editCorrectLabel = 'A';
    }

    /** Click on option to change correct answer (on the card, not modal) */
    public function setCorrectOption($questionId, $optionId)
    {
        McqOption::where('question_id', $questionId)->update(['is_correct' => false]);
        McqOption::where('id', $optionId)->update(['is_correct' => true]);
    }

    public function render()
    {
        return view('livewire.quiz-question-manager', [
            'questions' => $this->questions,
        ]);
    }
}
