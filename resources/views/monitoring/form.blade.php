<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('monitoring.form.page_title') }} – {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }

        @keyframes emergency-pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
                transform: scale(1);
            }
            50% {
                box-shadow: 0 0 0 20px rgba(239, 68, 68, 0);
                transform: scale(1.02);
            }
        }

        @keyframes emergency-shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
            20%, 40%, 60%, 80% { transform: translateX(2px); }
        }

        @keyframes emergency-glow {
            0%, 100% {
                filter: brightness(1);
            }
            50% {
                filter: brightness(1.2);
            }
        }

        .emergency-btn {
            animation: emergency-pulse 2s infinite, emergency-glow 2s infinite;
        }

        .emergency-btn:hover {
            animation: emergency-pulse 1s infinite, emergency-shake 0.5s ease-in-out, emergency-glow 1s infinite;
        }

        .modal-overlay {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            transform: scale(0.9) translateY(-20px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .modal-overlay.active .modal-content {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            <div class="border-b border-slate-200 px-6 py-5 sm:px-8 flex items-start justify-between gap-4 flex-wrap">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">{{ __('monitoring.form.heading') }}</h1>
                    <p class="mt-1 text-sm text-slate-500">{{ __('monitoring.form.agency') }}: {{ $agency->name }} · {{ __('monitoring.form.worker') }}: {{ $worker->fullname }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @include('monitoring.partials.locale-switcher')
                    <button type="button" id="emergency-btn" class="emergency-btn inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-500/50 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('monitoring.form.emergency') }}
                    </button>
                    <form action="{{ route('monitoring.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">{{ __('monitoring.form.logout') }}</button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="mx-6 mt-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 border border-emerald-200">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mx-6 mt-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-800 border border-red-200">
                    <p class="font-medium">{{ __('monitoring.errors.heading') }}</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('monitoring.form.store') }}" method="POST" class="space-y-6 px-6 py-6 sm:px-8 sm:py-8">
                @csrf

                <div class="absolute -left-[9999px] top-0" aria-hidden="true">
                    <label for="website">{{ __('monitoring.form.honeypot_label') }}</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">{{ __('monitoring.form.passport_number') }}</label>
                        <input type="text" value="{{ $worker->workerInformation?->passport_number }}" readonly class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-700 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">{{ __('monitoring.form.secret_code') }}</label>
                        <input type="text" value="{{ $worker->code }}" readonly class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-700 shadow-sm sm:text-sm">
                    </div>

                    <div class="sm:col-span-2 flex flex-wrap items-center gap-3">
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $latitude ?? '') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $longitude ?? '') }}">
                        <button type="button" id="use-location" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            {{ __('monitoring.form.use_location') }}
                        </button>
                        <span id="location-status" class="text-sm text-slate-500"></span>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="report" class="block text-sm font-medium text-slate-700">{{ __('monitoring.form.report_label') }} * <span class="text-slate-400 font-normal">{{ __('monitoring.form.report_max_chars') }}</span></label>
                        <textarea name="report" id="report" rows="10" maxlength="10000" required class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('report') }}</textarea>
                        <p class="mt-1 text-xs text-slate-500"><span id="report-count">0</span></p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('monitoring.form.submit_report') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="emergency-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="modal-content w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="bg-red-600 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-white/20 p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">{{ __('monitoring.form.emergency_modal_title') }}</h3>
                </div>
            </div>
            <div class="px-6 py-6">
                <p class="text-slate-700 mb-4">
                    {!! __('monitoring.form.emergency_modal_body', ['strong' => '<strong>'.e(__('monitoring.form.emergency_modal_body_strong')).'</strong>']) !!}
                </p>
                <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 mb-4">
                    <p class="text-sm text-amber-800 font-medium mb-2">{{ __('monitoring.form.emergency_location_required_title') }}</p>
                    <p class="text-sm text-amber-700">{{ __('monitoring.form.emergency_location_required_body') }}</p>
                </div>
                <div id="emergency-location-status" class="text-sm text-slate-600 mb-4 hidden">
                    <span class="inline-flex items-center gap-1">
                        <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('monitoring.form.getting_location') }}
                    </span>
                </div>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button type="button" id="cancel-emergency" class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                    {{ __('monitoring.form.cancel') }}
                </button>
                <button type="button" id="confirm-emergency" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-500/50 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ __('monitoring.form.confirm_emergency') }}
                </button>
            </div>
        </div>
    </div>

    <form id="emergency-form" action="{{ route('monitoring.emergency.store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="latitude" id="emergency-latitude">
        <input type="hidden" name="longitude" id="emergency-longitude">
    </form>

    @include('monitoring.partials.i18n-script')
    @vite('resources/js/app.js')
    <script>
        const i18n = window.monitoringI18n;

        function formatReportCharCount(count) {
            return i18n.reportCharCount.replace(':count', count);
        }

        function updateReportCount() {
            const report = document.getElementById('report');
            document.getElementById('report-count').textContent = formatReportCharCount(report.value.length);
        }

        document.getElementById('report').addEventListener('input', updateReportCount);
        updateReportCount();

        document.getElementById('use-location').addEventListener('click', function () {
            var status = document.getElementById('location-status');
            status.textContent = i18n.gettingLocation;
            if (!navigator.geolocation) {
                status.textContent = i18n.geolocationNotSupported;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    document.getElementById('latitude').value = pos.coords.latitude;
                    document.getElementById('longitude').value = pos.coords.longitude;
                    status.textContent = i18n.locationCaptured;
                },
                function () {
                    status.textContent = i18n.locationFailed;
                }
            );
        });

        const emergencyBtn = document.getElementById('emergency-btn');
        const emergencyModal = document.getElementById('emergency-modal');
        const cancelEmergency = document.getElementById('cancel-emergency');
        const confirmEmergency = document.getElementById('confirm-emergency');
        const emergencyLocationStatus = document.getElementById('emergency-location-status');
        const emergencyForm = document.getElementById('emergency-form');

        const gettingLocationHtml = `
            <span class="inline-flex items-center gap-1">
                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                ${i18n.gettingLocation}
            </span>
        `;

        emergencyBtn.addEventListener('click', function() {
            emergencyModal.classList.add('active');
            confirmEmergency.disabled = true;
            confirmEmergency.textContent = i18n.gettingLocationButton;
            emergencyLocationStatus.classList.remove('hidden');
            emergencyLocationStatus.innerHTML = gettingLocationHtml;

            if (!navigator.geolocation) {
                emergencyLocationStatus.innerHTML = `<span class="text-red-600">${i18n.geolocationNotSupported}</span>`;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    document.getElementById('emergency-latitude').value = pos.coords.latitude;
                    document.getElementById('emergency-longitude').value = pos.coords.longitude;
                    emergencyLocationStatus.innerHTML = `<span class="text-green-600">${i18n.locationCapturedEmergency}</span>`;
                    confirmEmergency.disabled = false;
                    confirmEmergency.textContent = i18n.confirmEmergency;
                },
                function () {
                    emergencyLocationStatus.innerHTML = `<span class="text-red-600">${i18n.locationFailedEmergency}</span>`;
                    confirmEmergency.disabled = true;
                    confirmEmergency.textContent = i18n.locationRequiredButton;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });

        cancelEmergency.addEventListener('click', function() {
            emergencyModal.classList.remove('active');
            document.getElementById('emergency-latitude').value = '';
            document.getElementById('emergency-longitude').value = '';
        });

        confirmEmergency.addEventListener('click', function() {
            if (!document.getElementById('emergency-latitude').value || !document.getElementById('emergency-longitude').value) {
                alert(i18n.locationAlert);
                return;
            }
            emergencyForm.submit();
        });

        emergencyModal.addEventListener('click', function(e) {
            if (e.target === emergencyModal) {
                emergencyModal.classList.remove('active');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && emergencyModal.classList.contains('active')) {
                emergencyModal.classList.remove('active');
            }
        });
    </script>
</body>
</html>
