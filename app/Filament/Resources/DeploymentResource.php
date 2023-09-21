<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeploymentResource\Pages;
use App\Models\Country;
use App\Models\Deployment;
use App\Models\ForeignAgency;
use App\Models\Handler;
use App\Models\Worker;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DeploymentResource extends Resource
{
    protected static ?string $model = Deployment::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('status')
                    ->columnSpanFull()
                    ->default('DEPLOYED')
                    ->readOnly(),
                Select::make('worker_id')
                    ->label('Worker')
                    ->options(Worker::tenant()->get()->pluck('fullname', 'id'))
                    ->required()
                    ->unique(ignorable: fn ($record) => $record)
                    ->searchable(),
                Select::make('foreign_agency_id')
                    ->label('F.R.A')
                    ->options(ForeignAgency::tenant()->get()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Select::make('handler_id')
                    ->label('Handler')
                    ->options(Handler::tenant()->get()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('position')
                    ->required(),
                Select::make('country')
                    ->required()
                    ->options(Country::tenant()->get()->pluck('country_name', 'id'))
                    ->searchable(),
                DatePicker::make('date_deployed')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worker.fullname')
                    ->sortable(['first_name'])
                    ->searchable(['workers.first_name', 'workers.last_name']),
                TextColumn::make('position')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('country')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date_deployed')
                    ->sortable()
                    ->date(),
                TextColumn::make('status')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('agency.name')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_deployed')
                    ->form([
                        DatePicker::make('date_deployed_from')->default(now()->startOfMonth()),
                        DatePicker::make('date_deployed_to')->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_deployed_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_deployed', '>=', $date),
                            )
                            ->when(
                                $data['date_deployed_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_deployed', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('Export')
                        ->icon('heroicon-o-cloud-arrow-down')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            return response()->streamDownload(function () use ($records) {
                                echo Pdf::loadHtml(
                                    view('downloadables.deployment-pdf', ['records' => $records->load('worker.workerInformation')])
                                )->setPaper('a4', 'landscape')->stream();
                            }, "Concern of Agency {$records->first()->date_deployed } .pdf");
                        }),
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
