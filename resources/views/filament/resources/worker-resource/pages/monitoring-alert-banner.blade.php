<div class="monitoring-alert-banner rounded-lg bg-danger-50 p-4 text-danger-600">
    <p class="text-base font-bold">⚠ Monitoring Alert</p>

    <div class="mt-2 space-y-1 text-sm font-medium">
        <div class="carousel-container overflow-hidden relative" style="min-height: 40px;">
            <div class="carousel-track flex transition-transform duration-500 ease-in-out" style="transform: translateX(0%);">
                @foreach ($workersNeedingMonitoring as $index => $worker)
                    <div class="carousel-item w-full flex-shrink-0 px-2 py-1">
                        {{ $worker->fullname }} is currently DEPLOYED under {{ $worker->agency?->name }} and has not submitted any monitoring report yet. Please follow up immediately.
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Optional indicators -->
        @if(count($workersNeedingMonitoring) > 1)
        <div class="carousel-indicators flex justify-center mt-2 space-x-2">
            @foreach ($workersNeedingMonitoring as $index => $worker)
                <button
                    type="button"
                    class="indicator w-2 h-2 rounded-full {{ $index === 0 ? 'bg-danger-600' : 'bg-gray-300' }}"
                    aria-label="Go to slide {{ $index + 1 }}"
                    onclick="goToSlide({{ $index }})">
                </button>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.carousel-track');
    const items = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.indicator');
    let currentIndex = 0;
    const totalItems = items.length;
    
    if (totalItems <= 1) {
        // No carousel needed if there's only one or no item
        console.log("Only one or no item, carousel not needed");
        return;
    }
    
    function updateCarousel() {
        // Calculate the translation amount
        const translateXValue = -(currentIndex * 100);
        track.style.transform = `translateX(${translateXValue}%)`;
        
        // Update indicators if they exist
        if (indicators.length > 0) {
            indicators.forEach((indicator, index) => {
                if (index === currentIndex) {
                    indicator.classList.add('bg-danger-600');
                    indicator.classList.remove('bg-gray-300');
                } else {
                    indicator.classList.add('bg-gray-300');
                    indicator.classList.remove('bg-danger-600');
                }
            });
        }
        
        console.log(`Showing slide ${currentIndex + 1} of ${totalItems}`);
    }
    
    function goToSlide(index) {
        if (index >= 0 && index < totalItems) {
            currentIndex = index;
            updateCarousel();
        }
    }
    
    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalItems;
        updateCarousel();
    }
    
    // Auto-slide every 2 seconds
    let slideInterval = setInterval(nextSlide, 2000);
    
    // Pause sliding when hovering over the carousel
    const container = document.querySelector('.carousel-container');
    if (container) {
        container.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });
        
        container.addEventListener('mouseleave', () => {
            slideInterval = setInterval(nextSlide, 2000);
        });
    }
    
    // Expose goToSlide globally so it can be called from HTML
    window.goToSlide = goToSlide;
    
    // Initialize the carousel
    updateCarousel();
    
    console.log("Carousel initialized with " + totalItems + " items");
});
</script>

<style>
    @keyframes monitoring-alert-glow {
        0%, 100% {
            box-shadow:
                inset 0 0 0 2px rgba(var(--danger-600), 1),
                0 0 0 0 rgba(var(--danger-600), 0.55);
        }

        50% {
            box-shadow:
                inset 0 0 0 2px rgba(var(--danger-600), 1),
                0 0 0 10px rgba(var(--danger-600), 0);
        }
    }

    .monitoring-alert-banner {
        animation:
            monitoring-alert-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite,
            pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    .carousel-container {
        height: auto;
        overflow: hidden;
        position: relative;
    }
    
    .carousel-track {
        display: flex;
        width: 100%;
        transition: transform 0.5s ease-in-out;
    }
    
    .carousel-item {
        width: 100%;
        flex-shrink: 0;
        padding: 0.5rem 0;
    }
    
    .carousel-indicators button {
        cursor: pointer;
        border: none;
    }
</style>
