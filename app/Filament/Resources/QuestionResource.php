<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Filament\Resources\QuestionResource\RelationManagers;
use App\Models\Question;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('answeredBy.name')
                    ->label('Answered By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('answered_at')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\IconColumn::make('isAnswered')
                    ->boolean()

            ])
            ->filters([
                //
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('answer')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('answer')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function ($data, $record) {
                        $record->update([
                            'answer' => $data['answer'],
                            'answered_by' => auth()->id(),
                            'answered_at' => now(),
                        ]);
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Textarea::make('body')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('answer')
                    ->columnSpanFull(),
                Forms\Components\Select::make('answered_by')
                    ->relationship('user', 'name'),
                Forms\Components\DateTimePicker::make('answered_at'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
