<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EducationResource\RelationManagers\EducationRelationManager;
use App\Filament\Resources\WorkerDocumentResource\RelationManagers\WorkerDocumentRelationManager;
use App\Filament\Resources\WorkerResource\Pages;
use App\Models\Worker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Tabs::make('Worker\'s Information')
                            ->tabs([
                                Tab::make('Name')
                                    ->schema([
                                        TextInput::make('first_name')->required(),
                                        TextInput::make('last_name')->required(),
                                        TextInput::make('middle_name')->required(),
                                        TextInput::make('code')->readOnly()->default('Auto generated after creation.'),
                                        Grid::make()
                                        ->relationship('workerInformation')
                                        ->schema([
                                            RichEditor::make('cover_letter')->columnSpanFull()->required(),
                                        ])

                                    ]),
                                Tab::make('Contact & Address')
                                    ->schema([
                                        Grid::make()
                                            ->relationship('workerInformation')
                                            ->schema([
                                                TextInput::make('email')->required(),
                                                TextInput::make('contact_number'),
                                                DatePicker::make('date_hired'),
                                                TextInput::make('address'),
                                                DatePicker::make('date_birth'),
                                                TextInput::make('place_birth'),

                                            ]),
                                    ]),
                                Tab::make('Passport')
                                    ->schema([
                                        Grid::make()
                                            ->relationship('workerInformation')
                                            ->schema([
                                                TextInput::make('passport_number'),
                                                TextInput::make('passport_place_issue'),
                                                DatePicker::make('passport_date_issue'),
                                                DatePicker::make('passport_date_expired'),
                                            ]),
                                    ]),
                                Tab::make('Relative Info')
                                    ->schema([
                                        Grid::make()
                                            ->relationship('workerInformation')
                                            ->schema([
                                                TextInput::make('father_name'),
                                                TextInput::make('father_occupation'),
                                                TextInput::make('mother_name'),
                                                TextInput::make('mother_occupation'),
                                                TextInput::make('spouse_name'),
                                                TextInput::make('spouse_occupation'),

                                            ]),
                                    ]),
                                Tab::make('Profile Status')
                                    ->schema([
                                        Grid::make()
                                            ->relationship('workerInformation')
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
                                            ]),
                                    ]),
                                Tab::make('File Upload')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('pic_face')
                                            ->collection('pic_face')->image(),
                                        SpatieMediaLibraryFileUpload::make('pic_body')
                                            ->collection('pic_body')->image(),
                                        SpatieMediaLibraryFileUpload::make('cv')
                                            ->collection('cv'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('middle_name')->sortable()->searchable(),
                TextColumn::make('agency.name')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime(),
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
            WorkerDocumentRelationManager::class,
            EducationRelationManager::class
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
