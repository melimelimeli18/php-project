<?php

namespace App\Filament\Resources\Quizzes\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;

class AttemptsRelationManager extends RelationManager
{
    protected static string $relationship = 'attempts';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('student.name', 'asc')
            ->columns([
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('student.class.name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('score')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_completed')
                    ->boolean(),
                TextColumn::make('submitted_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.name'),
                TextEntry::make('score'),
                TextEntry::make('submitted_at')->dateTime(),
                
                RepeatableEntry::make('answers')
                    ->label('Student Answers')
                    ->schema([
                        TextEntry::make('question.question_text')
                            ->label('Question'),
                        TextEntry::make('answer_display')
                            ->label('Student Answer')
                            ->default(fn ($record) => $record->selectedOption?->option_text ?? $record->answer_text ?? 'Not answered'),
                        IconEntry::make('is_correct')
                            ->label('Result')
                            ->boolean(),
                    ])
                    ->columns(3)
                    ->columnSpanFull()
            ]);
    }
}

