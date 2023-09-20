<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-center">
            <div>
                <h5 class="text-xl font-medium text-gray-500 dark:text-gray-400">Workers</h5>
                <div class="flex items-baseline text-gray-900 dark:text-white">
                    <span class="text-3xl font-extrabold tracking-tight">{{ $workerCount }}</span>
                </div>
            </div>
            <div style="margin-right: 5%; margin-left: 5%;">
                <h5 class="text-xl font-medium text-gray-500 dark:text-gray-400">Deployment</h5>
                <div class="flex items-baseline text-gray-900 dark:text-white">
                    <span class="text-3xl font-extrabold tracking-tight">{{ $deploymentCount }}</span>
                </div>
            </div>
            <div>
                <h5 class="text-xl font-medium text-gray-500 dark:text-gray-400">Applicantions</h5>
                <div class="flex items-baseline text-gray-900 dark:text-white">
                    <span class="text-3xl font-extrabold tracking-tight">{{ $applicationCount }}</span>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
