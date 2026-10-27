<?php

namespace App\Filament\Resources\HandlerResource\Pages;

use App\Filament\Resources\HandlerResource;
use App\Models\Handler;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\QueryException;

class EditHandler extends EditRecord
{
    protected static string $resource = HandlerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Handler')
                ->modalDescription('Are you sure you want to delete this handler? This action cannot be undone.')
                ->modalSubmitActionLabel('Yes, delete it')
                ->action(function (Handler $record) {
                    try {
                        $record->delete();
                        Notification::make()
                            ->success()
                            ->title('Handler deleted successfully')
                            ->send();
                    } catch (QueryException $e) {
                        if ($e->getCode() === '23000') {
                            Notification::make()
                                ->danger()
                                ->title('Cannot delete handler')
                                ->body('This handler has associated deployments. Please remove or reassign the deployments first before deleting this handler.')
                                ->persistent()
                                ->send();
                        } else {
                            throw $e;
                        }
                    }
                }),
        ];
    }
}
