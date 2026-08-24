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
            ->modalDescription(function (array $arguments): string {
                $workerId = $arguments['worker_id'] ?? null;
                $worker = Worker::find($workerId);

                if (! $worker) {
                    logger()->warning('Dashboard submit monitoring report opened with missing or invalid worker.', [
                        'worker_id' => $workerId,
                        'user_id' => auth()->id(),
                    ]);

                    return 'Worker not found. Please close this modal and try again from a valid worker record.' . json_encode($arguments);
                }

                return "Submit a monitoring report on behalf of {$worker->fullname}.";
            })
            ->modalSubmitActionLabel('Submit Report')
            ->modalSubmitAction(function (\Filament\Actions\StaticAction $action, array $arguments): \Filament\Actions\StaticAction {
                $workerExists = Worker::query()->whereKey($arguments['worker_id'] ?? null)->exists();

                return $action->disabled(! $workerExists);
            })
            ->disabledForm(function (array $arguments): bool {
                return ! Worker::query()->whereKey($arguments['worker_id'] ?? null)->exists();
            })
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
                $workerId = $arguments['worker_id'] ?? null;
                $worker = Worker::find($workerId);
                $hasActiveDeployment = $worker?->hasActiveDeployment() ?? false;

                if (! $worker || ! $hasActiveDeployment) {
                    logger()->warning('Dashboard submit monitoring report failed due to invalid worker state.', [
                        'worker_id' => $workerId,
                        'user_id' => auth()->id(),
                        'worker_exists' => (bool) $worker,
                        'has_active_deployment' => $hasActiveDeployment,
                    ]);

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