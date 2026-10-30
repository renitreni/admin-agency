<?php

namespace App\Filament\Resources\WorkerBlocklistResource\Pages;

use App\Filament\Resources\WorkerBlocklistResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateWorkerBlocklist extends CreateRecord
{
    protected static string $resource = WorkerBlocklistResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $data['agency_id'] = $user?->agency->first()?->id;
        $data['blocked_by'] = $user?->id;
        $data['blocked_at'] = now();
        $data['is_active'] = true;

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Worker added to blocklist')
            ->success()
            ->send();
    }
}
