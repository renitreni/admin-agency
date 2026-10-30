<?php

namespace App\Filament\Resources\WorkerBlocklistResource\Pages;

use App\Filament\Resources\WorkerBlocklistResource;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkerBlocklist extends ViewRecord
{
    protected static string $resource = WorkerBlocklistResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Worker Information')
                    ->schema([
                        TextEntry::make('first_name')->label('First Name'),
                        TextEntry::make('last_name')->label('Last Name'),
                        TextEntry::make('middle_name')->label('Middle Name'),
                        TextEntry::make('passport_number')->label('Passport Number'),
                        TextEntry::make('email')->label('Email'),
                        TextEntry::make('contact_number')->label('Contact Number'),
                        TextEntry::make('date_birth')->label('Date of Birth')->date(),
                        TextEntry::make('place_birth')->label('Place of Birth'),
                    ])
                    ->columns(2),

                Section::make('Blocklist Details')
                    ->schema([
                        TextEntry::make('reason')->label('Reason')->formatStateUsing(fn ($state) => nl2br($state)),
                        TextEntry::make('notes')->label('Notes')->formatStateUsing(fn ($state) => nl2br($state)),
                        TextEntry::make('match_confidence')->label('Match Confidence')->badge()
                            ->colors([
                                'danger' => 'high',
                                'warning' => 'medium',
                                'gray' => 'low',
                            ]),
                        TextEntry::make('is_active')->label('Status')->badge()
                            ->colors([
                                'success' => true,
                                'danger' => false,
                            ])
                            ->formatStateUsing(fn (bool $state) => $state ? 'Active' : 'Inactive'),
                        TextEntry::make('agency.name')->label('Blocklisted By Agency'),
                        TextEntry::make('blockedBy.name')->label('Blocked By'),
                        TextEntry::make('blocked_at')->label('Blocked At')->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
