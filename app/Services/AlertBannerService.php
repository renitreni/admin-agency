<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkerEmergency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AlertBannerService
{
    private MonitoringService $monitoringService;

    public function __construct()
    {
        $this->monitoringService = new MonitoringService();
    }

    /**
     * Get unresolved worker emergencies based on user type.
     */
    public function getWorkerEmergencies(?User $user = null): Collection
    {
        $user = $user ?? Auth::user();

        $query = WorkerEmergency::with(['worker', 'agency'])->unresolved()->latest();

        if ($this->isFraUser($user)) {
            $query->whereHas('worker', fn ($q) => $q->where('fra_id', $user->id));
        } else {
            $query->tenant();
        }

        return $query->get();
    }

    /**
     * Get workers needing monitoring based on user type.
     */
    public function getWorkersNeedingMonitoring(?User $user = null): Collection
    {
        $user = $user ?? Auth::user();

        return $this->isFraUser($user)
            ? $this->monitoringService->getWorkersNeedingMonitoringForFra($user)
            : $this->monitoringService->getWorkersNeedingMonitoring();
    }

    /**
     * Get both alerts for the dashboard.
     */
    public function getAllAlerts(?User $user = null): array
    {
        return [
            'workerEmergencies' => $this->getWorkerEmergencies($user),
            'workersNeedingMonitoring' => $this->getWorkersNeedingMonitoring($user),
        ];
    }

    /**
     * Check if user is an FRA type user.
     */
    private function isFraUser(?User $user): bool
    {
        return $user instanceof User && $user->user_type === User::TYPE_FRA;
    }
}