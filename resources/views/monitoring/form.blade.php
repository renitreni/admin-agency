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
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            <div class="border-b border-slate-200 px-6 py-5 sm:px-8 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">Worker Reporting Form</h1>
                    <p class="mt-1 text-sm text-slate-500">Agency: {{ $agency->name }} · Worker: {{ $worker->fullname }}</p>
                </div>
                <form action="{{ route('monitoring.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Logout</button>
                </form>
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
    </script>
</body>
</html>

