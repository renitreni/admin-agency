<?php

namespace App\Filament\Resources;

use App\Enums\CountryEnum;
use App\Enums\PositionEnum;
use App\Filament\Resources\DeploymentResource\Pages;
use App\Models\Deployment;
use App\Models\ForeignAgency;
use App\Models\Handler;
use App\Models\Worker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeploymentResource extends Resource
{
    protected static ?string $model = Deployment::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        $positions = [];
        foreach (PositionEnum::cases() as $item) {
            $positions[$item->value] = $item->value;
        }

        $countries = [];
        foreach (CountryEnum::cases() as $item) {
            $countries[$item->value] = $item->value;
        }

        return $form
            ->schema([
                TextInput::make('status')
                    ->columnSpanFull()
                    ->default('DEPLOYED')
                    ->readOnly(),
                Select::make('worker_id')
                    ->label('Worker')
                    ->options(Worker::all()->pluck('fullname', 'id'))
                    ->required()
                    ->unique()
                    ->searchable(),
                Select::make('foreign_agency_id')
                    ->label('F.R.A')
                    ->options(ForeignAgency::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('handler_id')
                    ->label('Handler')
                    ->options(Handler::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('position')
                    ->required()
                    ->options($positions),
                Select::make('country')
                    ->required()
                    ->options($countries)
                    ->searchable(),
                DatePicker::make('date_deployed')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worker.fullname')->sortable(['first_name'])->searchable(['first_name', 'last_name']),
                TextColumn::make('position')->sortable()->searchable(),
                TextColumn::make('country')->sortable()->searchable(),
                TextColumn::make('date_deployed')->sortable(),
                TextColumn::make('status')->sortable()->searchable(),
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
            'index' => Pages\ListDeployments::route('/'),
            'create' => Pages\CreateDeployment::route('/create'),
            'edit' => Pages\EditDeployment::route('/{record}/edit'),
        ];
    }
}
