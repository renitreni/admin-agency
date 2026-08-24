<?php

namespace App\Filament\Pages;

use App\Models\Monitoring;
use App\Models\User;
use App\Models\Worker;
use App\Services\AlertBannerService;
use Filament\Actions;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;

class Dashboard extends BaseDashboard
{
    /**
     * Render alert banners above the dashboard content.
     */
    public function getHeader(): ?View
    {
        $alertService = new AlertBannerService();
        $alerts = $alertService->getAllAlerts();

        if ($alerts['workerEmergencies']->isEmpty() && $alerts['workersNeedingMonitoring']->isEmpty()) {
            return null;
        }

        return view('filament.pages.dashboard-header', $alerts);
    }

    /**
     * Filament Action: Submit a monitoring report on behalf of a worker.
     * This action is visible only to agency and superadmin users (non-FRA).
     */
    public function submitMonitoringReport(): Actions\Action
    {
        return Actions\Action::make('submitMonitoringReport')
            ->label('Submit Report')
            ->modalHeading('Submit Monitoring Report')
            ->modalDescription('Submit a monitoring report on behalf of this worker.')
            ->modalSubmitActionLabel('Submit Report')
            ->form([
                Textarea::make('report')
                    ->label('Report')
                    ->required()
                    ->minLength(10)
                    ->maxLength(10000)
                    ->rows(4),
            ])
            ->arguments(['worker_id' => null])
            ->visible(function (): bool {
                $user = auth()->user();

                return $user instanceof User && $user->user_type !== User::TYPE_FRA;
            })
            ->action(function (array $arguments, array $data): void {
                $worker = Worker::find($arguments['worker_id'] ?? null);

                if (! $worker || ! $worker->hasActiveDeployment()) {
                    Notification::make()
                        ->title('Error')
                        ->body('Worker not found or not currently deployed.')
                        ->danger()
                        ->send();

                    return;
                }

                Monitoring::create([
                    'agency_id' => $worker->agency_id,
                    'worker_id' => $worker->id,
                    'passport_number' => (string) optional($worker->workerInformation)->passport_number,
                    'secret_code' => $worker->code,
                    'report' => $data['report'],
                    'latitude' => null,
                    'longitude' => null,
                    'reported_by' => auth()->id(),
                ]);

                Notification::make()
                    ->title('Report Submitted')
                    ->body('The monitoring report has been submitted successfully.')
                    ->success()
                    ->send();
            });
    }
}