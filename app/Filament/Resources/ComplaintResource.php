<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintResource\Pages;
use App\Models\Complaint;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'APIs';

    protected static ?string $modelLabel = 'Complaint';

    /** Complaints are submitted via public form; not scoped to a tenant agency. */
    protected static bool $isScopedToTenant = false;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('ofw_full_name')
                    ->label("OFW's Full Name")
                    ->searchable()
                    ->sortable(),
                TextColumn::make('foreign_recruitment_agency')
                    ->label('Foreign Recruitment Agency')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('email')->searchable(),
                TextColumn::make('primary_contact')->label('Primary Contact'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('viewOnMap')
                    ->label('View on Map')
                    ->icon('heroicon-o-map-pin')
                    ->url(fn (Complaint $record): ?string => $record->getGoogleMapsUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (Complaint $record): bool => $record->hasLocation()),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'view' => Pages\ViewComplaint::route('/{record}'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('foreign_recruitment_agency')
                    ->label('Foreign Recruitment Agency'),
                TextEntry::make('ofw_full_name')
                    ->label("OFW's Full Name"),
                TextEntry::make('gender'),
                TextEntry::make('birthdate')->date(),
                TextEntry::make('occupation'),
                TextEntry::make('nation_id')->label('Nation ID'),
                TextEntry::make('passport_no')->label('Passport No.'),
                TextEntry::make('email')->label('E-mail'),
                TextEntry::make('contact_person')->label('Contact Person'),
                TextEntry::make('primary_contact')->label('Primary Contact'),
                TextEntry::make('secondary_contact')->label('Secondary Contact'),
                TextEntry::make('address_abroad')->label('Address Abroad')->columnSpanFull(),
                TextEntry::make('latitude')->label('Latitude')->placeholder('—'),
                TextEntry::make('longitude')->label('Longitude')->placeholder('—'),
                TextEntry::make('complaint')
                    ->label('Complaint')
                    ->columnSpanFull()
                    ->html(),
                TextEntry::make('id')
                    ->label('Image Evidences')
                    ->formatStateUsing(function ($state, Complaint $record): string {
                        $media = $record->getMedia('image_evidences');
                        if ($media->isEmpty()) {
                            return '—';
                        }
                        return $media->map(fn ($m, $i) => '<a href="'.$m->getUrl().'" target="_blank" class="text-primary-600 hover:underline">Evidence '.($i + 1).'</a>')->join(', ');
                    })
                    ->html()
                    ->columnSpanFull(),
            ]);
    }
}