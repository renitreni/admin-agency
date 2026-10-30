<?php

namespace App\Filament\Resources\WorkerBlocklistResource\Pages;

use App\Filament\Resources\WorkerBlocklistResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditWorkerBlocklist extends EditRecord
{
    protected static string $resource = WorkerBlocklistResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public static function canAccess(array $parameters = []): bool
    {
        $record = static::resolveRecord($parameters['record'] ?? null);
        if (! $record) {
            return false;
        }

        $user = Auth::user();
        $userAgencyId = $user?->agency->first()?->id;

        return $userAgencyId && $record->agency_id === $userAgencyId;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Blocklist entry updated')
            ->success()
            ->send();
    }
}
