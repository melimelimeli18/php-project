<?php

namespace App\Filament\Resources\Subjects\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    protected static ?string $title = 'Chapter';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('order')
                ->label('Order / Nomor Bab')
                ->numeric()
                ->default(0)
                ->required(),

            TextInput::make('name')
                ->label('Chapter Name / Judul Bab')
                ->required()
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Chapter Name')
                    ->searchable(),

                TextColumn::make('questions_count')
                    ->label('Jumlah Soal')
                    ->counts('questions'),
            ])
            ->defaultSort('order', 'asc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription(fn ($record) => "Chapter ini digunakan oleh {$record->questions()->count()} soal. Jika dihapus, soal-soal tersebut tidak akan memiliki chapter."),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
