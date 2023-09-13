<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Models\Application;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Action::make('download')
                ->label('Download CV')
                ->icon('heroicon-m-arrow-down-tray')
                ->requiresConfirmation()
                ->action(fn (Application $record) => redirect()->to($record->getFirstMediaUrl('*'))),
        ];
    }
}
