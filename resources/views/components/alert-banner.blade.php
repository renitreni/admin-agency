@props([
    'type' => 'emergency', // 'emergency' or 'monitoring'
    'title' => '',
    'badge' => '',
    'items' => [],
    'badgeColor' => 'red',
])

@php
$colors = [
    'emergency' => [
        'bg' => 'bg-red-50',
        'text' => 'text-red-600',
        'border' => 'border-red-200',
        'badgeBg' => 'bg-red-100',
        'badgeText' => 'text-red-800',
        'indicatorActive' => 'bg-red-600',
        'buttonBg' => 'bg-red-100',
        'buttonText' => 'text-red-700',
        'buttonHover' => 'hover:bg-red-200',
    ],
    'monitoring' => [
        'bg' => 'bg-danger-50',
        'text' => 'text-danger-600',
        'border' => 'border-danger-200',
        'badgeBg' => 'bg-danger-100',
        'badgeText' => 'text-danger-800',
        'indicatorActive' => 'bg-danger-600',
        'buttonBg' => 'bg-danger-100',
        'buttonText' => 'text-danger-700',
        'buttonHover' => 'hover:bg-danger-200',
    ],
][$type];

$carouselId = uniqid($type . '-carousel-');
@endphp

<div class="{{ $type }}-alert-banner rounded-lg {{ $colors['bg'] }} p-4 {{ $colors['text'] }} border-2 {{ $colors['border'] }}">
    <div class="flex items-center gap-2 mb-2">
        <div class="animate-pulse">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <p class="text-base font-bold">{{ $title }}</p>
        @if($badge)
        <span class="ml-auto inline-flex items-center rounded-full {{ $colors['badgeBg'] }} px-2.5 py-0.5 text-xs font-medium {{ $colors['badgeText'] }} animate-pulse">
            {{ $badge }}
        </span>
        @endif
    </div>

    <div class="mt-2 space-y-1 text-sm font-medium" x-data="{
        currentIndex: 0,
        prevIndex: 0,
        totalItems: {{ count($items) }},
        interval: null,
        init() {
            if (this.totalItems <= 1) return;
            this.startAutoPlay();
            this.$watch('currentIndex', () => this.updateIndicators());
        },
        startAutoPlay() {
            this.interval = setInterval(() => {
                this.prevIndex = this.currentIndex;
                this.currentIndex = (this.currentIndex + 1) % this.totalItems;
            }, 4000);
        },
        stopAutoPlay() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
        },
        goToSlide(index) {
            this.prevIndex = this.currentIndex;
            this.currentIndex = index;
            this.stopAutoPlay();
            this.startAutoPlay();
        },
        getItemClass(index) {
            const isCurrent = this.currentIndex === index;
            const isPrev = this.prevIndex === index;
            
            // Determine direction: true when moving forward (next slide)
            const movingForward = (this.prevIndex < this.currentIndex) || 
                                 (this.prevIndex === this.totalItems - 1 && this.currentIndex === 0);
            
            if (isCurrent) {
                // Active item always in center with highest z-index
                return 'opacity-100 translate-x-0 z-20';
            }
            
            if (isPrev) {
                // Previous item slides out based on direction
                return movingForward 
                    ? 'opacity-0 -translate-x-full z-10'  // Slide out to left
                    : 'opacity-0 translate-x-full z-10';  // Slide out to right
            }
            
            // Non-active, non-previous items hide off-screen
            if (index < this.currentIndex) {
                return 'opacity-0 -translate-x-full z-0';
            } else {
                return 'opacity-0 translate-x-full z-0';
            }
        },
        updateIndicators() {
            this.$nextTick(() => {
                const indicators = this.$refs.indicators?.querySelectorAll('.indicator');
                if (!indicators) return;
                indicators.forEach((indicator, index) => {
                    if (index === this.currentIndex) {
                        indicator.classList.add('{{ $colors['indicatorActive'] }}');
                        indicator.classList.remove('bg-gray-300');
                    } else {
                        indicator.classList.add('bg-gray-300');
                        indicator.classList.remove('{{ $colors['indicatorActive'] }}');
                    }
                });
            });
        }
    }">
        <div
            class="carousel-container overflow-hidden relative"
            style="min-height: 40px;"
            @mouseenter="stopAutoPlay"
            @mouseleave="startAutoPlay"
        >
            @foreach ($items as $index => $item)
                <div
                    x-show="currentIndex === {{ $index }}"
                    x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-x-full"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-500"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-full"
                    class="carousel-item absolute inset-0 w-full px-2 py-1"
                >
                    {!! $item !!}
                </div>
            @endforeach
        </div>

        @if(count($items) > 1)
        <div class="carousel-indicators flex justify-center mt-2 space-x-2" x-ref="indicators">
            @foreach ($items as $index => $item)
                <button
                    type="button"
                    class="indicator w-2 h-2 rounded-full {{ $index === 0 ? $colors['indicatorActive'] : 'bg-gray-300' }}"
                    aria-label="Go to slide {{ $index + 1 }}"
                    @click="goToSlide({{ $index }})"
                ></button>
            @endforeach
        </div>
        @endif
    </div>
</div>
