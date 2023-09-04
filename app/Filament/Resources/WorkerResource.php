<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkerResource\Pages;
use App\Models\Worker;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Wizard::make([
                            Wizard\Step::make('Name')

                                ->schema([
                                    TextInput::make('first_name')->required(),
                                    TextInput::make('last_name')->required(),
                                    TextInput::make('middle_name')->required(),
                                ]),
                            Wizard\Step::make('Location')

                                ->schema([
                                    TextInput::make('contact_number'),
                                    DatePicker::make('workerInformation.date_hired')->required(),
                                    TextInput::make('address'),
                                    DatePicker::make('date_birth')->required(),
                                    TextInput::make('place_birth'),
                                ]),
                            Wizard\Step::make('Passport')
                                ->schema([
                                    TextInput::make('passport_number'),
                                    TextInput::make('passport_place_issue'),
                                    DatePicker::make('passport_date_issue'),
                                    DatePicker::make('passport_date_expired'),
                                ]),
                            Wizard\Step::make('Education')
                                ->schema([
                                    TextInput::make('elementary'),
                                    TextInput::make('high_school'),
                                    TextInput::make('vocational'),
                                    TextInput::make('college'),
                                ]),
                            Wizard\Step::make('Parents Info')
                                ->schema([
                                    TextInput::make('father_name'),
                                    TextInput::make('father_occupation'),
                                    TextInput::make('mother_name'),
                                    TextInput::make('mother_occupation'),
                                    TextInput::make('spouse_name'),
                                    TextInput::make('spouse_occupation'),
                                ]),
                            Wizard\Step::make('Profile Status')
                                ->schema([
                                    Select::make('gender')->options([
                                        'male' => 'Male',
                                        'female' => 'Female',
                                    ]),
                                    TextInput::make('religion'),
                                    Select::make('civil_status')->options([
                                        'single' => 'Single',
                                        'married' => 'Married',
                                        'divorced' => 'Divorced',
                                        'widowed' => 'Widowed',
                                    ]),
                                    TextInput::make('height')->numeric(),
                                    TextInput::make('weight')->numeric(),
                                    Textarea::make('objectives')
                                        ->rows(10)
                                        ->cols(20),
                                ]),
                            Wizard\Step::make('File Upload')
                                ->schema([
                                    FileUpload::make('pic_face')
                                        ->image()
                                        ->imageResizeMode('cover'),
                                    FileUpload::make('pic_body')
                                        ->image()
                                        ->imageResizeMode('cover'),
                                ]),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->dateTime(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('agency.name')->sortable()->searchable(),
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
            'index' => Pages\ListWorkers::route('/'),
            'create' => Pages\CreateWorker::route('/create'),
            'edit' => Pages\EditWorker::route('/{record}/edit'),
        ];
    }
}
