<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            </form>
        </div>
    </div>

    @vite('resources/js/app.js')
</body>
</html>

