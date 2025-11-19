@extends('layouts.staff')

@section('title', 'Dashboard Staf')

@push('styles')
<style>
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }

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

  .stat-card {
    animation: fadeIn 0.5s ease-out;
  }
</style>
@endpush

@section('content')
<div class="space-y-6">
  <!-- Welcome Header -->
  <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
        <p class="text-gray-600">Ringkasan aktiviti dan tempahan terkini.</p>
      </div>
      <div class="hidden md:flex items-center gap-4">
        <img src="{{ asset('IMG/PROTON.png') }}" alt="Proton" class="h-12">
        <img src="{{ asset('IMG/ADTCMLK.png') }}" alt="ADTEC" class="h-12">
      </div>
    </div>
  </div>

  <!-- Event Banner Carousel -->
  <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-blue-600">campaign</span>
      Event & Pengumuman
    </h2>

    <div class="relative w-full h-96 overflow-hidden rounded-xl shadow-lg bg-gray-300">
      <div class="relative w-full h-full">
        @if(isset($eventBanners) && $eventBanners->count() > 0)
          @foreach($eventBanners as $index => $banner)
            <div class="carousel-slide absolute top-0 left-0 w-full h-full {{ $index === 0 ? 'active' : '' }}">
              <img src="{{ $banner->banner_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover" />
              <!-- Gradient Overlay -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
              <!-- Content -->
              <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                <h2 class="text-4xl font-bold mb-3 drop-shadow-lg">{{ $banner->title }}</h2>
                <p class="text-lg opacity-95 drop-shadow-md">{{ $banner->description }}</p>
              </div>
            </div>
          @endforeach

          <!-- Navigation Buttons (show only if more than 1 banner) -->
          @if($eventBanners->count() > 1)
            <button class="carousel-btn prev absolute top-1/2 -translate-y-1/2 left-4 bg-white/30 backdrop-blur-md hover:bg-white/50 border-0 text-white text-2xl w-12 h-12 rounded-full cursor-pointer z-10 transition-all duration-300 flex items-center justify-center" onclick="changeSlide(-1)">
              <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="carousel-btn next absolute top-1/2 -translate-y-1/2 right-4 bg-white/30 backdrop-blur-md hover:bg-white/50 border-0 text-white text-2xl w-12 h-12 rounded-full cursor-pointer z-10 transition-all duration-300 flex items-center justify-center" onclick="changeSlide(1)">
              <span class="material-symbols-outlined">chevron_right</span>
            </button>

            <!-- Carousel Indicators -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
              @foreach($eventBanners as $index => $banner)
                <div class="indicator w-3 h-3 rounded-full bg-white/50 cursor-pointer transition-all duration-300 hover:bg-white/80 {{ $index === 0 ? 'active bg-white' : '' }}" onclick="goToSlide({{ $index }})"></div>
              @endforeach
            </div>
          @endif
        @else
          <div class="carousel-slide active absolute top-0 left-0 w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-900">
            <div class="text-center text-white p-8">
              <span class="material-symbols-outlined text-6xl mb-4 opacity-50">event_busy</span>
              <h2 class="text-3xl font-bold mb-3">Tiada Event Terbaharu</h2>
              <p class="text-lg opacity-90">Nantikan kemas kini akan datang daripada pihak pentadbir.</p>
            </div>
          </div>
        @endif
      </div>
    </div>

    <!-- Event Info -->
    <div class="mt-4 text-center text-gray-700">
      @if(isset($eventBanners) && $eventBanners->count() > 0)
        <p class="flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-blue-600">info</span>
          Menunjukkan <span class="font-semibold text-blue-600 mx-1">{{ $eventBanners->count() }}</span> event aktif
        </p>
      @else
        <p class="flex items-center justify-center gap-2">
          <span class="material-symbols-outlined text-gray-500">schedule</span>
          Maklumat akan dikemas kini secara automatik
        </p>
      @endif
    </div>
  </div>

  <!-- Quick Stats (Optional - can add booking stats here) -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Total Bookings -->
    <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-blue-100 text-sm font-medium mb-1">Jumlah Tempahan</p>
          <h3 class="text-4xl font-bold">{{ $totalBookings ?? '0' }}</h3>
        </div>
        <div class="bg-white/20 rounded-full p-4">
          <span class="material-symbols-outlined text-4xl">calendar_month</span>
        </div>
      </div>
    </div>

    <!-- Pending Bookings -->
    <div class="stat-card bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-amber-100 text-sm font-medium mb-1">Menunggu Kelulusan</p>
          <h3 class="text-4xl font-bold">{{ $pendingBookings ?? '0' }}</h3>
        </div>
        <div class="bg-white/20 rounded-full p-4">
          <span class="material-symbols-outlined text-4xl">pending</span>
        </div>
      </div>
    </div>

    <!-- Completed Today -->
    <div class="stat-card bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-xl p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-emerald-100 text-sm font-medium mb-1">Selesai Hari Ini</p>
          <h3 class="text-4xl font-bold">{{ $completedToday ?? '0' }}</h3>
        </div>
        <div class="bg-white/20 rounded-full p-4">
          <span class="material-symbols-outlined text-4xl">task_alt</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-blue-600">bolt</span>
      Tindakan Pantas
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <a href="{{ route('staff.booking') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl transition-all duration-300 group">
        <span class="material-symbols-outlined text-5xl text-blue-600 mb-2 group-hover:scale-110 transition-transform">car_rental</span>
        <span class="text-gray-800 font-semibold">Tempahan Baharu</span>
      </a>

      <a href="{{ route('staff.notification') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-xl transition-all duration-300 group">
        <span class="material-symbols-outlined text-5xl text-purple-600 mb-2 group-hover:scale-110 transition-transform">notifications</span>
        <span class="text-gray-800 font-semibold">Notifikasi</span>
      </a>

      <a href="{{ route('staff.history') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-xl transition-all duration-300 group">
        <span class="material-symbols-outlined text-5xl text-green-600 mb-2 group-hover:scale-110 transition-transform">history</span>
        <span class="text-gray-800 font-semibold">Sejarah</span>
      </a>

      <a href="{{ route('staff.profile') }}" class="flex flex-col items-center justify-center p-6 bg-gradient-to-br from-pink-50 to-pink-100 hover:from-pink-100 hover:to-pink-200 rounded-xl transition-all duration-300 group">
        <span class="material-symbols-outlined text-5xl text-pink-600 mb-2 group-hover:scale-110 transition-transform">person</span>
        <span class="text-gray-800 font-semibold">Profil Saya</span>
      </a>
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
