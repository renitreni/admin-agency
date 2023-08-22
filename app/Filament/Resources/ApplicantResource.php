<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicantResource\Pages;
use App\Filament\Resources\ApplicantResource\RelationManagers;
use App\Models\Applicant;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicantResource extends Resource
{
    protected static ?string $model = Applicant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')->required(),
                TextInput::make('last_name')->required(),
                TextInput::make('contact_number'),
                DatePicker::make('date_hired')->required(),
                TextInput::make('address'),
                DatePicker::make('date_birth')->required(),
                TextInput::make('place_birth'),
                TextInput::make('passport_number'),
                TextInput::make('passport_place_issue'),
                DatePicker::make('passport_date_issue'),
                DatePicker::make('passport_date_expired'),
                TextInput::make('elementary'),
                TextInput::make('high_school'),
                TextInput::make('vocational'),
                TextInput::make('college'),
                TextInput::make('father_name'),
                TextInput::make('father_occupation'),
                TextInput::make('mother_name'),
                TextInput::make('mother_occupation'),
                TextInput::make('spouse_name'),
                TextInput::make('spouse_occupation'),
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
                FileUpload::make('pic_face')
                    ->image()
                    ->imageResizeMode('cover'),
                FileUpload::make('pic_body')
                    ->image()
                    ->imageResizeMode('cover'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->searchable(),
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->sortable()->searchable(),
                TextColumn::make('contact_number')->sortable()->searchable(),
                TextColumn::make('passport_number')->sortable()->searchable(),
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
            'index' => Pages\ListApplicants::route('/'),
            'create' => Pages\CreateApplicant::route('/create'),
            'edit' => Pages\EditApplicant::route('/{record}/edit'),
        ];
    }
}
