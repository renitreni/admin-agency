<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="/manifest.json">
    <title>{{ __('monitoring.login.page_title') }} – {{ config('app.name') }}</title>
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
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900">{{ __('monitoring.login.heading') }}</h1>
                        <p class="mt-1 text-sm text-slate-500">{{ __('monitoring.login.subtitle') }}</p>
                    </div>
                    @include('monitoring.partials.locale-switcher')
                </div>
            </div>

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

            <form action="{{ route('monitoring.login.authenticate') }}" method="POST" class="space-y-6 px-6 py-6 sm:px-8 sm:py-8">
                @csrf

                <div class="grid gap-6">
                    <div>
                        <label for="passport_number" class="block text-sm font-medium text-slate-700">{{ __('monitoring.login.passport_number') }} *</label>
                        <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}" required
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="secret_code" class="block text-sm font-medium text-slate-700">{{ __('monitoring.login.secret_code') }} *</label>
                        <input type="text" name="secret_code" id="secret_code" value="{{ old('secret_code') }}" required inputmode="numeric" maxlength="5" pattern="\d{5}"
                            class="mt-1 block w-full rounded-lg border border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        {{ __('monitoring.login.continue') }}
                    </button>
                    <a href="{{ url('/') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('monitoring.login.cancel') }}</a>
                </div>

                <!-- PWA Install Button Section -->
                <div class="mt-6 rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 p-4 text-center">
                    <p class="mb-3 text-sm text-slate-600">{{ __('monitoring.login.pwa_install_hint') }}</p>
                    <button type="button" id="installBtn" class="inline-flex items-center rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-green-700 hover:shadow-lg active:translate-y-0.5 active:shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span id="installBtnText">{{ __('monitoring.login.pwa_install_button') }}</span>
                    </button>
                    <p id="installStatus" class="mt-2 hidden text-xs text-slate-500"></p>
                </div>
            </form>
        </div>
    </div>

    @include('monitoring.partials.i18n-script')
    @vite('resources/js/app.js')
    <script>
        const i18n = window.monitoringI18n;
        let deferredPrompt;
        const installBtn = document.getElementById('installBtn');

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/service-worker.js')
                .then((registration) => {
                    console.log('Service Worker registered:', registration);
                })
                .catch((error) => {
                    console.log('Service Worker registration failed:', error);
                });
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installBtn.disabled = false;
            document.getElementById('installStatus').classList.add('hidden');
        });

        if (window.matchMedia('(display-mode: standalone)').matches) {
            installBtn.disabled = true;
            installBtn.classList.add('opacity-50', 'cursor-not-allowed');
            document.getElementById('installBtnText').textContent = i18n.appInstalled;
            document.getElementById('installStatus').textContent = i18n.appAlreadyInstalled;
            document.getElementById('installStatus').classList.remove('hidden');
        }

        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) {
                document.getElementById('installStatus').textContent = i18n.installNotAvailable;
                document.getElementById('installStatus').classList.remove('hidden');
                return;
            }

            deferredPrompt.prompt();

            const { outcome } = await deferredPrompt.userChoice;

            if (outcome === 'accepted') {
                console.log('User accepted the install prompt');
                installBtn.disabled = true;
                document.getElementById('installBtnText').textContent = i18n.installing;
            } else {
                console.log('User dismissed the install prompt');
                document.getElementById('installStatus').textContent = i18n.installCancelled;
                document.getElementById('installStatus').classList.remove('hidden');
            }

            deferredPrompt = null;
        });

        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed');
            installBtn.classList.add('hidden');
            deferredPrompt = null;
        });
    </script>
</body>
</html>
