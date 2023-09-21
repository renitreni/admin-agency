<?php

namespace App\Filament\Resources\EducationResource\RelationManagers;

use App\Enums\EducationLevelEnum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EducationRelationManager extends RelationManager
{
    protected static string $relationship = 'education';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('level')
                    ->required()
                    ->options(EducationLevelEnum::class),
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\DatePicker::make('from_date'),
                Forms\Components\DatePicker::make('to_date'),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Education')
            ->columns([
                Tables\Columns\TextColumn::make('level'),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('from_date'),
                Tables\Columns\TextColumn::make('to_date'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }
}
