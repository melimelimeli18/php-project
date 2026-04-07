<?php

namespace App\Filament\Resources\Quizzes\Pages;

use App\Filament\Resources\Quizzes\QuizResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ManageQuizQuestions extends Page
{
    use InteractsWithRecord;

    protected static string $resource = QuizResource::class;

    protected string $view = 'filament.resources.quizzes.pages.manage-quiz-questions';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_template')
                ->label('Download Template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn () => \Maatwebsite\Excel\Facades\Excel::download(
                    new \App\Exports\QuestionTemplateExport, 
                    'question-template.xlsx'
                )),
                
            \Filament\Actions\Action::make('import_excel')
                ->label('Import from Excel')
                ->icon('heroicon-o-document-arrow-up')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('Excel File')
                        ->disk('public')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $file = \Illuminate\Support\Facades\Storage::disk('public')->path($data['file']);
                    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\QuestionImport($this->record), $file);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Import Successful')
                        ->body('Questions have been imported and attached to the quiz.')
                        ->success()
                        ->send();
                        
                    $this->dispatch('questionsImported');
                }),
                
            \Filament\Actions\Action::make('add_to_chapter')
                ->label('Add to Chapter')
                ->icon('heroicon-o-folder-plus')
                ->color('info')
                ->action(fn () => $this->dispatch('open-assign-chapter-modal')),
        ];
    }
}
