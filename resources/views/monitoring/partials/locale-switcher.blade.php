@php
    $currentLocale = app()->getLocale();
@endphp
<div class="flex items-center gap-2 text-sm" role="group" aria-label="{{ __('monitoring.locale.switch_label') }}">
    <span class="text-slate-500">{{ __('monitoring.locale.switch_label') }}:</span>
    <form action="{{ route('monitoring.locale.switch') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="locale" value="en">
        <button type="submit"
            class="rounded-md px-2.5 py-1 font-medium {{ $currentLocale === 'en' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            {{ __('monitoring.locale.english') }}
        </button>
    </form>
    <form action="{{ route('monitoring.locale.switch') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="locale" value="fil">
        <button type="submit"
            class="rounded-md px-2.5 py-1 font-medium {{ $currentLocale === 'fil' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100' }}">
            {{ __('monitoring.locale.filipino') }}
        </button>
    </form>
</div>
