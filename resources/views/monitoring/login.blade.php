<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.json">
    <title>Worker Monitoring Login – {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="mx-auto max-w-xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            <div class="border-b border-slate-200 px-6 py-5 sm:px-8">
                <h1 class="text-2xl font-semibold text-slate-900">Worker Reporting Login</h1>
                <p class="mt-1 text-sm text-slate-500">Input your passport number and 5-digit secret code to submit a monitoring report.</p>
            </div>

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

            <form action="{{ route('monitoring.login.authenticate') }}" method="POST" class="space-y-6 px-6 py-6 sm:px-8 sm:py-8">
                @csrf

                <div class="grid gap-6">
                    <div>
                        <label for="passport_number" class="block text-sm font-medium text-slate-700">Passport Number *</label>
                        <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="secret_code" class="block text-sm font-medium text-slate-700">Secret Code (5 digits) *</label>
                        <input type="text" name="secret_code" id="secret_code" value="{{ old('secret_code') }}" required inputmode="numeric" maxlength="5" pattern="\d{5}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Continue
                    </button>
                    <a href="{{ url('/') }}" class="text-sm text-slate-600 hover:text-slate-900">Cancel</a>
                </div>

                <!-- PWA Install Button Section -->
                <div class="mt-6 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-4 text-center">
                    <p class="mb-3 text-sm text-slate-600">Install this app on your device for quick access</p>
                    <button type="button" id="installBtn" class="inline-flex items-center rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-green-700 hover:shadow-lg active:translate-y-0.5 active:shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span id="installBtnText">Download & Install App</span>
                    </button>
                    <p id="installStatus" class="mt-2 hidden text-xs text-slate-500"></p>
                </div>
            </form>
        </div>
    </div>

    @vite('resources/js/app.js')
    <script>
        // PWA Installation Logic
        let deferredPrompt;
        const installBtn = document.getElementById('installBtn');

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then((registration) => {
                    console.log('Service Worker registered:', registration);
                })
                .catch((error) => {
                    console.log('Service Worker registration failed:', error);
                });
        }

        // Listen for the beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent the mini-infobar from appearing on mobile
            e.preventDefault();
            // Store the event for later use
            deferredPrompt = e;
            // Enable the install button
            installBtn.disabled = false;
            document.getElementById('installStatus').classList.add('hidden');
        });

        // Check if app is already installed
        if (window.matchMedia('(display-mode: standalone)').matches) {
            installBtn.disabled = true;
            installBtn.classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('installBtnText').textContent = 'App Installed';
            document.getElementById('installStatus').textContent = 'This app is already installed on your device';
            document.getElementById('installStatus').classList.remove('hidden');
        }

        // Handle install button click
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) {
                // Show message if install is not available
                document.getElementById('installStatus').textContent = 'Installation not available. Please use your browser menu to install.';
                document.getElementById('installStatus').classList.remove('hidden');
                return;
            }
            
            // Show the install prompt
            deferredPrompt.prompt();
            
            // Wait for the user to respond to the prompt
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('User accepted the install prompt');
                installBtn.disabled = true;
                document.getElementById('installBtnText').textContent = 'Installing...';
            } else {
                console.log('User dismissed the install prompt');
                document.getElementById('installStatus').textContent = 'Installation cancelled. Click the button to try again.';
                document.getElementById('installStatus').classList.remove('hidden');
            }
            
            // Clear the deferred prompt
            deferredPrompt = null;
        });

        // Listen for the appinstalled event
        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed');
            installBtn.classList.add('hidden');
            deferredPrompt = null;
        });
    </script>
</body>
</html>

