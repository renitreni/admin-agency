<!-- Emergency Alert Banner: Shows only unresolved worker emergencies -->
<div class="emergency-alert-banner rounded-lg bg-red-50 p-4 text-red-600 border-2 border-red-200">
    <div class="flex items-center gap-2 mb-2">
        <div class="animate-pulse">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
        </div>
        <p class="text-base font-bold">🚨 EMERGENCY ALERT</p>
        <span class="ml-auto inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 animate-pulse">
            ACTIVE
        </span>
    </div>

    <div class="mt-2 space-y-1 text-sm font-medium">
        <div class="carousel-container overflow-hidden relative" style="min-height: 40px;">
            <div class="emergency-carousel-track flex transition-transform duration-500 ease-in-out" style="transform: translateX(0%);">
                @foreach ($workerEmergencies as $index => $emergency)
                    <div class="emergency-carousel-item w-full flex-shrink-0 px-2 py-1">
                        <strong>{{ $emergency->worker->fullname }}</strong> ({{ $emergency->passport_number }})
                        under <strong>{{ $emergency->agency?->name }}</strong>
                        sent an EMERGENCY alert
                        <span class="text-red-700">({{ $emergency->created_at->diffForHumans() }})</span>.
                        Location:
                        @if($emergency->hasLocation())
                            <a href="{{ $emergency->getGoogleMapsUrl() }}" target="_blank" class="underline hover:text-red-800">View on Map</a>
                        @else
                            Not available
                        @endif
                        <a href="{{ \App\Filament\Resources\WorkerEmergencyResource::getUrl('view', ['record' => $emergency]) }}" class="ml-2 inline-flex items-center rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700 hover:bg-red-200">
                            View Details →
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Optional indicators -->
        @if(count($workerEmergencies) > 1)
        <div class="emergency-carousel-indicators flex justify-center mt-2 space-x-2">
            @foreach ($workerEmergencies as $index => $emergency)
                <button
                    type="button"
                    class="emergency-indicator w-2 h-2 rounded-full {{ $index === 0 ? 'bg-red-600' : 'bg-gray-300' }}"
                    aria-label="Go to slide {{ $index + 1 }}"
                    onclick="goToEmergencySlide({{ $index }})">
                </button>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.emergency-carousel-track');
    const items = document.querySelectorAll('.emergency-carousel-item');
    const indicators = document.querySelectorAll('.emergency-indicator');
    let currentIndex = 0;
    const totalItems = items.length;

    if (totalItems <= 1) {
        return;
    }

    function updateCarousel() {
        const translateXValue = -(currentIndex * 100);
        track.style.transform = `translateX(${translateXValue}%)`;

        if (indicators.length > 0) {
            indicators.forEach((indicator, index) => {
                if (index === currentIndex) {
                    indicator.classList.add('bg-red-600');
                    indicator.classList.remove('bg-gray-300');
                } else {
                    indicator.classList.add('bg-gray-300');
                    indicator.classList.remove('bg-red-600');
                }
            });
        }
    }

    function goToEmergencySlide(index) {
        if (index >= 0 && index < totalItems) {
            currentIndex = index;
            updateCarousel();
        }
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        updateCarousel();
    }

    let slideInterval = setInterval(nextSlide, 4000);

    const container = document.querySelector('.emergency-carousel-track').closest('.carousel-container');
    if (container) {
        container.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });

        container.addEventListener('mouseleave', () => {
            slideInterval = setInterval(nextSlide, 4000);
        });
    }

    window.goToEmergencySlide = goToEmergencySlide;

    updateCarousel();
});
</script>

<style>
    @keyframes emergency-alert-glow {
        0%, 100% {
            box-shadow:
                inset 0 0 0 2px rgba(220, 38, 38, 1),
                0 0 0 0 rgba(220, 38, 38, 0.55);
        }

        50% {
            box-shadow:
                inset 0 0 0 2px rgba(220, 38, 38, 1),
                0 0 0 10px rgba(220, 38, 38, 0);
        }
    }

    @keyframes emergency-border-pulse {
        0%, 100% {
            border-color: rgba(252, 165, 165, 1);
        }

        50% {
            border-color: rgba(220, 38, 38, 1);
        }
    }

    .emergency-alert-banner {
        animation:
            emergency-alert-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite,
            emergency-border-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .emergency-carousel-track {
        display: flex;
        width: 100%;
        transition: transform 0.5s ease-in-out;
    }

    .emergency-carousel-item {
        width: 100%;
        flex-shrink: 0;
        padding: 0.5rem 0;
    }

    .emergency-carousel-indicators button {
        cursor: pointer;
        border: none;
    }
</style>