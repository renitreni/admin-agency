<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use App\Services\WorkerBlocklistService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateWorker extends CreateRecord
{
    protected static string $resource = WorkerResource::class;

    protected function afterCreate(): void
    {
        $service = app(WorkerBlocklistService::class);
        $result = WorkerResource::checkBlocklistMatches($this->record);

        if ($result['is_blocklisted']) {
            $match = $result['matches'][0] ?? null;
            if ($match) {
                Notification::make()
                    ->title('Warning: Worker matches blocklist entry')
                    ->body("This worker matches a blocklist entry from {$match['entry']->agency->name} (confidence: {$match['confidence']}).")
                    ->warning()
                    ->persistent()
                    ->send();
            }
        } elseif (! empty($result['matches'])) {
            // Medium/low confidence matches - show info notification
            $match = $result['matches'][0] ?? null;
            if ($match) {
                Notification::make()
                    ->title('Note: Potential blocklist match found')
                    ->body("This worker has a potential match with a blocklist entry from {$match['entry']->agency->name} (confidence: {$match['confidence']}). Please review.")
                    ->info()
                    ->persistent()
                    ->send();
            }
        }
    }
}
