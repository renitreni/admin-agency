<?php

namespace App\Filament\Resources\VoucherItemResource\RelationManagers;

use App\Models\VoucherTypes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherItemRelationManager extends RelationManager
{
    protected static string $relationship = 'voucherItems';

    public function getHeaderWidgets(): array
    {
        return [
            VoucherItemOverview
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('voucher_type')
                ->required()
                ->options(VoucherTypes::tenant()->get()->pluck('name', 'name'))
                ->unique(ignorable: fn ($record) => $record),
                Forms\Components\TextInput::make('remarks')
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('VoucherItem')
            ->columns([
                Tables\Columns\TextColumn::make('voucher_type'),
                Tables\Columns\TextColumn::make('remarks'),
                Tables\Columns\TextColumn::make('amount'),
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
