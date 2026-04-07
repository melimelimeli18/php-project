<?php

namespace App\Filament\Resources\SchoolClasses\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolClassesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('grade')
                    ->searchable(),
                TextColumn::make('join_code')
                    ->label('Join Code')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('regenerate_code')
                    ->label('Regenerate Code')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->modalDescription('Old code becomes invalid immediately. Existing students in this class are not affected.')
                    ->action(function ($record) {
                        do {
                            $code = strtoupper(\Illuminate\Support\Str::random(6));
                        } while (\App\Models\SchoolClass::where('join_code', $code)->exists());
                        $record->update(['join_code' => $code]);
                        \Filament\Notifications\Notification::make()->title('Kode berhasil diperbarui')->success()->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
