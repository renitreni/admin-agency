<?php

namespace App\Filament\Resources\WorkerBlocklistResource\Pages;

use App\Filament\Resources\WorkerBlocklistResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;

class ListWorkerBlocklists extends ListRecords
{
    protected static string $resource = WorkerBlocklistResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $userAgencyId = $user?->agency->first()?->id;

        return [
            Action::make('viewOwn')
                ->label('My Agency Blocklist')
                ->icon('heroicon-o-building-office')
                ->url(static::getResource()::getUrl('index', ['agency' => 'own']))
                ->color('primary')
                ->visible(fn () => request()->query('agency') !== 'own' && $userAgencyId),

            Action::make('viewOther')
                ->label('Other Agencies Blocklist')
                ->icon('heroicon-o-globe-alt')
                ->url(static::getResource()::getUrl('index', ['agency' => 'other']))
                ->color('gray')
                ->visible(fn () => request()->query('agency') !== 'other' && $userAgencyId),
        ];
    }
}
