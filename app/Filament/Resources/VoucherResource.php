<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Filament\Resources\VoucherResource\RelationManagers;
use App\Models\ForeignAgency;
use App\Models\Voucher;
use App\Models\Worker;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('worker_id')
                    ->label('Worker')
                    ->options(Worker::tenant()->get()->pluck('fullname', 'id'))
                    ->unique(ignorable: fn ($record) => $record)
                    ->required()
                    ->searchable(),
                Select::make('foreign_agency_id')
                    ->label('F.R.A')
                    ->options(ForeignAgency::tenant()->get()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('source'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worker.fullname')->searchable(['first_name', 'last_name']),
                TextColumn::make('foreignAgency.name')->searchable(),
                TextColumn::make('source')->searchable(),
                TextColumn::make('created_by')->searchable(),
                TextColumn::make('updated_by')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit' => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
