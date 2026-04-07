<?php

namespace App\Livewire;

use App\Models\Chapter;
use App\Models\Question;
use App\Models\Quiz;
use Livewire\Component;

class AssignChapterModal extends Component
{
    public Quiz $quiz;
    public ?int $selectedChapterId = null;
    public array $selectedQuestionIds = [];
    public int $step = 1;
    public bool $open = false;

    protected $listeners = ['open-assign-chapter-modal' => 'openModal'];

    public function mount(Quiz $quiz)
    {
        $this->quiz = $quiz;
    }

    public function openModal()
    {
        $this->reset(['selectedChapterId', 'selectedQuestionIds']);
        $this->step = 1;
        $this->open = true;
    }

    public function selectChapter(int $chapterId): void
    {
        $this->selectedChapterId = $chapterId;
        $this->step = 2;
    }

    public function save(): void
    {
        if ($this->selectedChapterId && !empty($this->selectedQuestionIds)) {
            Question::whereIn('id', $this->selectedQuestionIds)
                ->update(['chapter_id' => $this->selectedChapterId]);
        }

        $this->reset(['selectedChapterId', 'selectedQuestionIds', 'step']);
        $this->open = false;

        $this->dispatch('chapters-updated');
    }

    public function back(): void
    {
        $this->step = 1;
        $this->selectedChapterId = null;
        $this->selectedQuestionIds = [];
    }

    public function getChaptersProperty()
    {
        return Chapter::where('subject_id', $this->quiz->subject_id)
            ->orderBy('order')
            ->get();
    }

    public function getUntaggedQuestionsProperty()
    {
        return Question::whereHas('quizzes', function ($q) {
                $q->where('quiz_id', $this->quiz->id);
            })
            ->whereNull('chapter_id')
            ->get();
    }

    public function render()
    {
        return view('livewire.assign-chapter-modal');
    }
}
