<?php

namespace App\Filament\Resources;

use App\Enums\ConcernStatusEnum;
use App\Filament\Resources\ConcernResource\Pages;
use App\Filament\Resources\ConcernResource\RelationManagers\ConcernReportRelationManager;
use App\Models\Agency;
use App\Models\Concern;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ConcernResource extends Resource
{
    protected static ?string $model = Concern::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->columnSpanFull(),
                Select::make('agency_id')
                    ->options(Agency::all()->pluck('name', 'id'))
                    ->relationship('agency', 'name'),
                Select::make('status')->options(ConcernStatusEnum::class)->required(),
                RichEditor::make('description')->required()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('agency.name')->sortable()->searchable(),
                TextColumn::make('status')->sortable(),
                TextColumn::make('created_at')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Model $record) {

                        $record->load('concernReport');

                        return response()->streamDownload(function () use ($record) {
                            echo Pdf::loadHtml(
                                view('downloadables.concern', ['record' => $record])
                            )->stream();
                        }, "{$record->created_at->format('F j, Y')} Concern of Agency {$record->agency->name} .pdf");
                    }),
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
            ConcernReportRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConcerns::route('/'),
            'create' => Pages\CreateConcern::route('/create'),
            'edit' => Pages\EditConcern::route('/{record}/edit'),
        ];
    }
}
