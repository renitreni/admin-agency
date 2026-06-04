<x-filament-panels::layout.base :livewire="$livewire">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet">

    <div class="auth-login-layout min-h-screen grid lg:grid-cols-2">
        {{-- Brand panel --}}
        <div class="auth-login-brand relative flex flex-col items-center justify-center overflow-hidden px-8 py-10 lg:px-12 lg:py-16">
            <div class="auth-login-brand-glow" aria-hidden="true"></div>
            <div class="auth-login-brand-grid" aria-hidden="true"></div>

            <div class="relative z-10 flex flex-col items-center text-center lg:items-start lg:text-left">
                {{-- <img
                    src="{{ asset('images/1010-logo.jpg') }}"
                    alt="{{ config('app.name') }}"
                    class="auth-login-logo h-16 w-16 rounded-xl object-cover shadow-lg ring-2 ring-white/20 lg:h-24 lg:w-24"
                /> --}}
                <h1 class="mt-5 text-2xl font-semibold tracking-tight text-white lg:text-3xl">
                    {{ config('app.name') }}
                </h1>
                <p class="mt-2 max-w-xs text-sm text-blue-100 lg:text-base">
                    Quality Management System
                </p>
            </div>
        </div>

        {{-- Form panel --}}
        <div class="auth-login-form flex min-h-[calc(100vh-8rem)] flex-col items-center justify-center px-6 py-10 sm:px-10 lg:min-h-screen lg:px-16">
            <main class="auth-login-form-inner w-full max-w-md animate-fade-in">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $livewire->getRenderHookScopes()) }}
</x-filament-panels::layout.base>
