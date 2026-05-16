<div class="monitoring-alert-banner rounded-lg bg-danger-50 p-4 text-danger-600">
    <p class="text-base font-bold">⚠ Monitoring Alert</p>

    <ul class="mt-2 space-y-1 text-sm font-medium">
        @foreach ($workersNeedingMonitoring as $worker)
            <li>
                {{ $worker->fullname }} is currently DEPLOYED under {{ $worker->agency?->name }} and has not submitted any monitoring report yet. Please follow up immediately.
            </li>
        @endforeach
    </ul>
</div>

<style>
    @keyframes monitoring-alert-glow {
        0%, 100% {
            box-shadow:
                inset 0 0 0 2px rgba(var(--danger-600), 1),
                0 0 0 0 rgba(var(--danger-600), 0.55);
        }

        50% {
            box-shadow:
                inset 0 0 0 2px rgba(var(--danger-600), 1),
                0 0 0 10px rgba(var(--danger-600), 0);
        }
    }

    .monitoring-alert-banner {
        animation:
            monitoring-alert-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite,
            pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
