<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Worker Monitoring Report – {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }

        /* Emergency Button Animations */
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

        /* Modal Animation */
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
                    <h1 class="text-2xl font-semibold text-slate-900">Worker Reporting Form</h1>
                    <p class="mt-1 text-sm text-slate-500">Agency: {{ $agency->name }} · Worker: {{ $worker->fullname }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Emergency Button -->
                    <button type="button" id="emergency-btn" class="emergency-btn inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-500/50 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        EMERGENCY
                    </button>
                    <form action="{{ route('monitoring.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Logout</button>
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
                    <p class="font-medium">Please correct the following errors:</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('monitoring.form.store') }}" method="POST" class="space-y-6 px-6 py-6 sm:px-8 sm:py-8">
                @csrf

                {{-- Honeypot: hidden from users, bots often fill it --}}
                <div class="absolute -left-[9999px] top-0" aria-hidden="true">
                    <label for="website">Leave blank</label>
                    <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Passport Number</label>
                        <input type="text" value="{{ $worker->workerInformation?->passport_number }}" readonly class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-700 shadow-sm sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Secret Code</label>
                        <input type="text" value="{{ $worker->code }}" readonly class="mt-1 block w-full rounded-lg border border-slate-200 bg-slate-100 text-slate-700 shadow-sm sm:text-sm">
                    </div>

                    <div class="sm:col-span-2 flex flex-wrap items-center gap-3">
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $latitude ?? '') }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $longitude ?? '') }}">
                        <button type="button" id="use-location" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Use my location
                        </button>
                        <span id="location-status" class="text-sm text-slate-500"></span>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="report" class="block text-sm font-medium text-slate-700">Monitoring Report * <span class="text-slate-400 font-normal">(max 10,000 characters)</span></label>
                        <textarea name="report" id="report" rows="10" maxlength="10000" required class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('report') }}</textarea>
                        <p class="mt-1 text-xs text-slate-500"><span id="report-count">0</span> / 10,000 characters</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Emergency Confirmation Modal -->
    <div id="emergency-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="modal-content w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="bg-red-600 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="rounded-full bg-white/20 p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">Emergency Alert</h3>
                </div>
            </div>
            <div class="px-6 py-6">
                <p class="text-slate-700 mb-4">
                    Are you sure you want to send an <strong>EMERGENCY ALERT</strong>? This will immediately notify the agency of your distress.
                </p>
                <div class="rounded-lg bg-amber-50 border border-amber-200 p-4 mb-4">
                    <p class="text-sm text-amber-800 font-medium mb-2">⚠️ Location Required</p>
                    <p class="text-sm text-amber-700">Your current location will be captured and sent with this emergency alert. Please ensure location services are enabled.</p>
                </div>
                <div id="emergency-location-status" class="text-sm text-slate-600 mb-4 hidden">
                    <span class="inline-flex items-center gap-1">
                        <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Getting your location...
                    </span>
                </div>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button type="button" id="cancel-emergency" class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                    Cancel
                </button>
                <button type="button" id="confirm-emergency" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-red-500/50 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    Yes, Send Emergency
                </button>
            </div>
        </div>
    </div>

    <!-- Emergency Form (Hidden) -->
    <form id="emergency-form" action="{{ route('monitoring.emergency.store') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="latitude" id="emergency-latitude">
        <input type="hidden" name="longitude" id="emergency-longitude">
    </form>

    @vite('resources/js/app.js')
    <script>
        document.getElementById('report').addEventListener('input', function () {
            document.getElementById('report-count').textContent = this.value.length;
        });
        document.getElementById('report-count').textContent = document.getElementById('report').value.length;

        document.getElementById('use-location').addEventListener('click', function () {
            var status = document.getElementById('location-status');
            status.textContent = 'Getting location…';
            if (!navigator.geolocation) {
                status.textContent = 'Geolocation is not supported by your browser.';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    document.getElementById('latitude').value = pos.coords.latitude;
                    document.getElementById('longitude').value = pos.coords.longitude;
                    status.textContent = 'Location captured.';
                },
                function () {
                    status.textContent = 'Could not get location. You can still submit without it.';
                }
            );
        });

        // Emergency Button Logic
        const emergencyBtn = document.getElementById('emergency-btn');
        const emergencyModal = document.getElementById('emergency-modal');
        const cancelEmergency = document.getElementById('cancel-emergency');
        const confirmEmergency = document.getElementById('confirm-emergency');
        const emergencyLocationStatus = document.getElementById('emergency-location-status');
        const emergencyForm = document.getElementById('emergency-form');

        emergencyBtn.addEventListener('click', function() {
            emergencyModal.classList.add('active');
            // Reset button state
            confirmEmergency.disabled = true;
            confirmEmergency.textContent = 'Getting location...';
            emergencyLocationStatus.classList.remove('hidden');
            emergencyLocationStatus.innerHTML = `
                <span class="inline-flex items-center gap-1">
                    <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Getting your location...
                </span>
            `;

            // Get location for emergency
            if (!navigator.geolocation) {
                emergencyLocationStatus.innerHTML = '<span class="text-red-600">❌ Geolocation is not supported by your browser.</span>';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    document.getElementById('emergency-latitude').value = pos.coords.latitude;
                    document.getElementById('emergency-longitude').value = pos.coords.longitude;
                    emergencyLocationStatus.innerHTML = '<span class="text-green-600">✅ Location captured successfully.</span>';
                    confirmEmergency.disabled = false;
                    confirmEmergency.textContent = 'Yes, Send Emergency';
                },
                function () {
                    emergencyLocationStatus.innerHTML = '<span class="text-red-600">❌ Could not get location. Please enable location services and try again.</span>';
                    confirmEmergency.disabled = true;
                    confirmEmergency.textContent = 'Location Required';
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
            // Clear location inputs
            document.getElementById('emergency-latitude').value = '';
            document.getElementById('emergency-longitude').value = '';
        });

        confirmEmergency.addEventListener('click', function() {
            if (!document.getElementById('emergency-latitude').value || !document.getElementById('emergency-longitude').value) {
                alert('Location is required for emergency alerts. Please enable location services.');
                return;
            }
            emergencyForm.submit();
        });

        // Close modal when clicking outside
        emergencyModal.addEventListener('click', function(e) {
            if (e.target === emergencyModal) {
                emergencyModal.classList.remove('active');
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && emergencyModal.classList.contains('active')) {
                emergencyModal.classList.remove('active');
            }
        });
    </script>
</body>
</html>

