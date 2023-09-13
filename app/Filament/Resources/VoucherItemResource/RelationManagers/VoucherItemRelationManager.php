<?php

namespace App\Filament\Resources\VoucherItemResource\RelationManagers;

use App\Models\VoucherTypes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Livewire\Component;

class VoucherItemRelationManager extends RelationManager
{
    protected static string $relationship = 'voucherItems';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('voucher_type')
                    ->required()
                    ->options(VoucherTypes::tenant()->get()->pluck('name', 'name')),
                Forms\Components\TextInput::make('remarks')
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
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
                Tables\Actions\CreateAction::make()
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshVoucher');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshVoucher');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshVoucher');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (Component $livewire) {
                            $livewire->dispatch('refreshVoucher');
                        }),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (Component $livewire) {
                        $livewire->dispatch('refreshVoucher');
                    }),
            ]);
    }
}
