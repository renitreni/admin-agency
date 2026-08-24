<!-- Dashboard Alert Banners: Emergency and Monitoring Alerts -->
@php
/**
 * @var \Illuminate\Support\Collection $workerEmergencies
 * @var \Illuminate\Support\Collection $workersNeedingMonitoring
 */
@endphp

@php
    $user = auth()->user();
    $showSubmitReportButton = $user instanceof \App\Models\User && $user->user_type !== \App\Models\User::TYPE_FRA;
@endphp


<div class="space-y-4 mb-6">
    {{-- Emergency Alert Banner --}}
    @if($workerEmergencies->isNotEmpty())
        <x-alert-banner
            type="emergency"
            title="🚨 EMERGENCY ALERT"
            badge="ACTIVE"
            :items="$workerEmergencies->map(function($emergency) {
                $locationLink = $emergency->hasLocation()
                    ? '<a href=\'' . $emergency->getGoogleMapsUrl() . '\' target=\'_blank\' class=\'underline hover:text-red-800\'>View on Map</a>'
                    : 'Not available';

                return '<strong>' . $emergency->worker->fullname . '</strong> (' . $emergency->passport_number . ')
                    under <strong>' . ($emergency->agency?->name ?? 'N/A') . '</strong>
                    sent an EMERGENCY alert
                    <span class=\'text-red-700\'>(' . $emergency->created_at->diffForHumans() . ')</span>.
                    Location: ' . $locationLink . '
                    <a href=\'' . \App\Filament\Resources\WorkerEmergencyResource::getUrl('view', ['record' => $emergency]) . '\'
                       class=\'ml-2 inline-flex items-center rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 hover:bg-red-200\'>
                        View Details →
                    </a>';
            })->toArray()" />
    @endif

    {{-- Monitoring Alert Banner --}}
    @if($workersNeedingMonitoring->isNotEmpty())
        @include('filament.resources.worker-resource.pages.monitoring-alert-banner', [
            'workersNeedingMonitoring' => $workersNeedingMonitoring,
            'showSubmitReportButton' => $showSubmitReportButton,
            'showWorkerLink' => true,
        ])
    @endif
</div>