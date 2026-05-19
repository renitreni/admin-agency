<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BaseResource;
use App\Filament\Resources\DeployedWorkerForResource\Pages;
use App\Filament\Resources\EducationResource\RelationManagers\EducationRelationManager;
use App\Filament\Resources\SkillsResource\RelationManagers\SkillRelationManager;
use App\Filament\Resources\WorkerDocumentResource\RelationManagers\WorkerDocumentRelationManager;
use App\Filament\Resources\WorkHistoryResource\RelationManagers\WorkHistoryRelationManager;
use App\Models\Worker;
use App\Services\WorkerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

/**
 * @property-read User $user The authenticated user
 */
class DeployedWorkerForResource extends BaseResource
{
    protected static ?string $model = Worker::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'General Settings';

    protected static ?string $navigationLabel = 'Deployed Workers';

    protected static ?string $modelLabel = 'Deployed Worker';

    protected static ?string $pluralModelLabel = 'Deployed Workers';

    protected static ?string $slug = 'deployed-workers';
    
    // This resource shows workers that are deployed and related to the current logged-in user role FRA

    protected static bool $isScopedToTenant = false;

    public static function getEloquentQuery(): Builder
    {
        // Get the current user's foreign agency IDs if they are FRA type
        $user = Auth::user();
        
        if ($user && $user->user_type === 'FRA') {
            // Get the foreign agency ID of the current user
            $foreignAgencyIds = $user->foreignAgencies()->pluck('foreign_agencies.id');
            
            if (!$foreignAgencyIds->isEmpty()) {
                return parent::getEloquentQuery()
                    ->whereHas('deployments', function ($query) use ($foreignAgencyIds) {
                        $query->whereIn('foreign_agency_id', $foreignAgencyIds);
                    });
            }
        }
        
        // Return an empty query for non-FRA users
        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        return $user && $user->user_type === 'FRA';
    }

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && $user->user_type === 'FRA';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

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
                                        Grid::make()
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('first_name')->required()->columns(1),
                                                TextInput::make('last_name')->required()->columns(1),
                                                TextInput::make('middle_name')->required()->columns(1),
                                                TextInput::make('position')->required(),
                                                TextInput::make('code')
                                                    ->label('Secret Code (5 digits)')
                                                    ->readOnly()
                                                    ->dehydrated(false)
                                                    ->formatStateUsing(fn (?string $state): string => $state ?: 'Generated after worker is created.')
                                                    ->suffixAction(
                                                        FormAction::make('generate_new_secret_code')
                                                            ->icon('heroicon-o-arrow-path')
                                                            ->tooltip('Generate new secret code')
                                                            ->requiresConfirmation()
                                                            ->visible(fn (?Worker $record): bool => filled($record))
                                                            ->action(function (Set $set, ?Worker $record): void {
                                                                if (! $record) {
                                                                    return;
                                                                }

                                                                $newCode = WorkerService::regenerateCode($record);
                                                                $set('code', $newCode);

                                                                Notification::make()
                                                                    ->title('Secret code regenerated.')
                                                                    ->success()
                                                                    ->send();
                                                            })
                                                    )
                                                    ->helperText('This code is used by the worker in the external reporting login.'),
                                            ]),
                                        Grid::make()
                                            ->relationship('workerInformation')
                                            ->schema([
                                                RichEditor::make('cover_letter')
                                                    ->label('Cover Letter / Objective')
                                                    ->columnSpanFull()
                                                    ->required(),
                                            ]),

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
                                        SpatieMediaLibraryFileUpload::make('passport')
                                            ->label('Passport image')
                                            ->collection('passport')
                                            ->image()
                                            ->imagePreviewHeight('200')
                                            ->maxSize(10240)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->helperText('Original file is saved as-is. Re-uploading with the same filename overwrites the previous image (max 10MB).'),
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
                TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('deployments_position')
                    ->label('Position')
                    ->getStateUsing(function ($record) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if (!$user || $user->user_type !== 'FRA') return null;
                        
                        $foreignAgencyId = $user->foreignAgencies()->first()?->id;
                        if (!$foreignAgencyId) return null;
                        
                        return $record->deployments()->where('foreign_agency_id', $foreignAgencyId)->first()?->position;
                    })
                    ->sortable(),
                TextColumn::make('deployments_country')
                    ->label('Country')
                    ->getStateUsing(function ($record) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if (!$user || $user->user_type !== 'FRA') return null;
                        
                        $foreignAgencyId = $user->foreignAgencies()->first()?->id;
                        if (!$foreignAgencyId) return null;
                        
                        return $record->deployments()->where('foreign_agency_id', $foreignAgencyId)->first()?->country;
                    }),
                TextColumn::make('deployments_date_deployed')
                    ->label('Date Deployed')
                    ->date()
                    ->getStateUsing(function ($record) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if (!$user || $user->user_type !== 'FRA') return null;
                        
                        $foreignAgencyId = $user->foreignAgencies()->first()?->id;
                        if (!$foreignAgencyId) return null;
                        
                        return $record->deployments()->where('foreign_agency_id', $foreignAgencyId)->first()?->date_deployed;
                    })
                    ->sortable(),
                TextColumn::make('deployments_status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $user = \Illuminate\Support\Facades\Auth::user();
                        if (!$user || $user->user_type !== 'FRA') return null;
                        
                        $foreignAgencyId = $user->foreignAgencies()->first()?->id;
                        if (!$foreignAgencyId) return null;
                        
                        return $record->deployments()->where('foreign_agency_id', $foreignAgencyId)->first()?->status;
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'completed' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('generate_cv')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->requiresConfirmation()
                    ->action(function (Model $records) {
                        return response()->streamDownload(function () use ($records) {
                            $records = $records->load(['education', 'workHistory']);
                            $cvTemplate = $records->agency->getFirstMediaUrl('cv_template');

                            echo Pdf::loadHtml(view('downloadables.cv', compact('records', 'cvTemplate')))
                                ->stream();
                        }, "CV {$records->last_name}, {$records->first_name}.pdf");
                    }),
            ])
            ->bulkActions([
                ExportBulkAction::make(),
            ])
            ->modifyQueryUsing(function ($query) {
                $user = \Illuminate\Support\Facades\Auth::user();
                if ($user && $user->user_type === 'FRA') {
                    $foreignAgencyIds = $user->foreignAgencies()->pluck('foreign_agencies.id');
                    $query->with(['deployments' => function ($query) use ($foreignAgencyIds) {
                        $query->whereIn('foreign_agency_id', $foreignAgencyIds);
                    }]);
                }
            });
    }

    public static function getRelations(): array
    {
        return [
            EducationRelationManager::class,
            WorkHistoryRelationManager::class,
            SkillRelationManager::class,
            WorkerDocumentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeployedWorkers::route('/'),
            // Note: We don't include edit/create routes since they are disabled,
            // and there might not be a dedicated view page
        ];
    }
}