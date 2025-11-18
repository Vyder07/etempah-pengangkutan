@extends('layouts.staff')

@section('title', 'Dashboard Staf')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .carousel-slide {
    opacity: 0;
    transition: opacity 0.6s ease-in-out;
  }

  .carousel-slide.active {
    opacity: 1;
    z-index: 1;
  }

  .carousel-btn:hover {
    transform: translateY(-50%) scale(1.1);
  }

  .indicator.active {
    transform: scale(1.3);
  }
</style>
@endpush

@section('content')
<div class="flex justify-center items-center w-full">
  <div class="max-w-[1200px] bg-[rgb(251,252,255)] border-4 border-white rounded-[20px] shadow-[0_6px_18px_rgba(0,0,0,0.1)] flex flex-col items-center p-5 mx-auto opacity-95 relative overflow-hidden">

    <!-- Carousel Container -->
    <div class="relative w-full h-[320px] overflow-hidden rounded-xl shadow-[0_4px_10px_rgba(0,0,0,0.15)] bg-gray-300">
      <div class="relative w-full h-full">
        @if(isset($eventBanners) && $eventBanners->count() > 0)
          @foreach($eventBanners as $index => $banner)
            <div class="carousel-slide absolute top-0 left-0 w-full h-full flex justify-center items-center {{ $index === 0 ? 'active' : '' }}">
              <img src="{{ $banner->banner_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover brightness-[0.85]" />
              <div class="absolute bottom-5 left-[30px] right-[30px] text-white z-[2]" style="text-shadow: 0 2px 6px rgba(0,0,0,0.5);">
                <h2 class="m-0 text-[1.8em] font-semibold">{{ $banner->title }}</h2>
                <p class="mt-1.5 text-base opacity-95">{{ $banner->description }}</p>
              </div>
            </div>
          @endforeach

          <!-- Navigation Buttons (show only if more than 1 banner) -->
          @if($eventBanners->count() > 1)
            <button class="carousel-btn prev absolute top-1/2 -translate-y-1/2 left-[15px] bg-white/30 backdrop-blur-[10px] border-0 text-white text-2xl w-[50px] h-[50px] rounded-full cursor-pointer z-10 transition-all duration-300 flex items-center justify-center hover:bg-white/50" onclick="changeSlide(-1)">
              <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="carousel-btn next absolute top-1/2 -translate-y-1/2 right-[15px] bg-white/30 backdrop-blur-[10px] border-0 text-white text-2xl w-[50px] h-[50px] rounded-full cursor-pointer z-10 transition-all duration-300 flex items-center justify-center hover:bg-white/50" onclick="changeSlide(1)">
              <span class="material-symbols-outlined">chevron_right</span>
            </button>

            <!-- Carousel Indicators -->
            <div class="absolute bottom-[15px] left-1/2 -translate-x-1/2 flex gap-2 z-10">
              @foreach($eventBanners as $index => $banner)
                <div class="indicator w-2.5 h-2.5 rounded-full bg-white/50 cursor-pointer transition-all duration-300 {{ $index === 0 ? 'active bg-white' : '' }}" onclick="goToSlide({{ $index }})"></div>
              @endforeach
            </div>
          @endif
        @else
          <div class="carousel-slide active absolute top-0 left-0 w-full h-full flex justify-center items-center">
            <img src="https://via.placeholder.com/900x300?text=Tiada+Event+Terbaharu" alt="Event Banner" class="w-full h-full object-cover brightness-[0.85]" />
            <div class="absolute bottom-5 left-[30px] right-[30px] text-white z-[2]" style="text-shadow: 0 2px 6px rgba(0,0,0,0.5);">
              <h2 class="m-0 text-[1.8em] font-semibold">Tiada Event Terbaharu</h2>
              <p class="mt-1.5 text-base opacity-95">Nantikan kemas kini akan datang daripada pihak pentadbir.</p>
            </div>
          </div>
        @endif
      </div>
    </div>

    <!-- Event Details -->
    <div class="mt-4 text-center text-[#333] text-[1.05rem]">
      @if(isset($eventBanners) && $eventBanners->count() > 0)
        <p>Menunjukkan <span class="font-semibold">{{ $eventBanners->count() }}</span> event aktif. Klik penunjuk untuk melihat butiran lain.</p>
      @else
        <p>Maklumat akan dikemas kini secara automatik apabila pentadbir menambah event baharu.</p>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Carousel functionality
  let currentSlide = 0;
  const slides = document.querySelectorAll('.carousel-slide');
  const indicators = document.querySelectorAll('.indicator');
  const totalSlides = slides.length;

  // Auto-play interval (5 seconds)
  let autoPlayInterval;

  function showSlide(index) {
    // Remove active class from all slides and indicators
    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(indicator => indicator.classList.remove('active'));

    // Wrap around if index is out of bounds
    if (index >= totalSlides) {
      currentSlide = 0;
    } else if (index < 0) {
      currentSlide = totalSlides - 1;
    } else {
      currentSlide = index;
    }

    // Add active class to current slide and indicator
    if (slides[currentSlide]) {
      slides[currentSlide].classList.add('active');
    }
    if (indicators[currentSlide]) {
      indicators[currentSlide].classList.add('active');
    }
  }

  function changeSlide(direction) {
    showSlide(currentSlide + direction);
    resetAutoPlay();
  }

  function goToSlide(index) {
    showSlide(index);
    resetAutoPlay();
  }

  function startAutoPlay() {
    if (totalSlides > 1) {
      autoPlayInterval = setInterval(() => {
        showSlide(currentSlide + 1);
      }, 5000); // Change slide every 5 seconds
    }
  }

  function resetAutoPlay() {
    clearInterval(autoPlayInterval);
    startAutoPlay();
  }

  // Start auto-play when page loads
  if (totalSlides > 1) {
    startAutoPlay();
  }

  // Pause auto-play on hover
  const carouselContainer = document.querySelector('.carousel-container');
  if (carouselContainer) {
    carouselContainer.addEventListener('mouseenter', () => {
      clearInterval(autoPlayInterval);
    });

    carouselContainer.addEventListener('mouseleave', () => {
      startAutoPlay();
    });
  }

  // Keyboard navigation
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') {
      changeSlide(-1);
    } else if (e.key === 'ArrowRight') {
      changeSlide(1);
    }
  });
</script>
@endpush
