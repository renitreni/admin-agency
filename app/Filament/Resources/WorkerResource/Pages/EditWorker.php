<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use App\Filament\Resources\WorkerResource;
use App\Services\WorkerBlocklistService;
use App\Services\WorkerService;
use Filament\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditWorker extends EditRecord
{
    protected static string $resource = WorkerResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $userAgencyId = $user?->agency->first()?->id;
        $canBlocklist = $userAgencyId && $this->record->agency_id === $userAgencyId;

        return [
            Actions\Action::make('regenerate_secret_code')
                ->label('Generate New Secret Code')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (): void {
                    WorkerService::regenerateCode($this->record);
                    $this->refreshFormData(['code']);

                    Notification::make()
                        ->title('Secret code regenerated.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('blocklist_worker')
                ->label('Blocklist Worker')
                ->icon('heroicon-o-user-minus')
                ->color('danger')
                ->visible($canBlocklist)
                ->form([
                    Section::make('Blocklist Details')
                        ->schema([
                            Textarea::make('reason')
                                ->label('Reason for Blocklisting')
                                ->required()
                                ->rows(4)
                                ->maxLength(1000),
                            Textarea::make('notes')
                                ->label('Additional Notes')
                                ->rows(3)
                                ->maxLength(1000),
                            Select::make('match_confidence')
                                ->label('Match Confidence')
                                ->options([
                                    'high' => 'High',
                                    'medium' => 'Medium',
                                    'low' => 'Low',
                                ])
                                ->default('medium')
                                ->required(),
                        ]),
                ])
                ->action(function (array $data): void {
                    $service = app(WorkerBlocklistService::class);
                    $service->blocklistWorker($data, Auth::user(), $this->record);

                    Notification::make()
                        ->title('Worker added to blocklist')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Blocklist Worker')
                ->modalDescription('This will add the worker to your agency\'s blocklist. Other agencies will be warned if they try to add this worker.'),
            Actions\DeleteAction::make(),
        ];
    }
}
