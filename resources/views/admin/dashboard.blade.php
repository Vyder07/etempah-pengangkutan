@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('search-placeholder', 'Cari tempahan, event...')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
    .banner-slide {
        display: none;
    }
    .banner-slide.active {
        display: block;
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .indicator {
        transition: all 0.3s ease;
    }
    .indicator.active {
        background: #fff !important;
        width: 32px;
    }
</style>
@endpush

@section('content')
<!-- Banner Carousel -->
<div class="relative w-full h-96 overflow-hidden rounded-2xl shadow-2xl bg-gradient-to-br from-gray-800 to-gray-900 mb-8">
    <div class="relative w-full h-full" id="bannerCarousel">
        @if($banners->count() > 0)
            @foreach($banners as $index => $banner)
            <div class="banner-slide {{ $index === 0 ? 'active' : '' }} relative w-full h-full" data-banner-id="{{ $banner->id }}">
                <img src="{{ $banner->banner_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                <div class="absolute bottom-8 left-8 right-8 text-white z-10">
                    <h2 class="text-4xl font-bold mb-2 drop-shadow-2xl">{{ $banner->title }}</h2>
                    <p class="text-lg opacity-95 drop-shadow-lg max-w-2xl">{{ $banner->description }}</p>
                </div>
            </div>
            @endforeach

            <!-- Navigation arrows -->
            @if($banners->count() > 1)
            <button class="absolute top-1/2 -translate-y-1/2 left-4 bg-white/20 hover:bg-white/40 backdrop-blur-sm border-none rounded-full w-14 h-14 flex items-center justify-center cursor-pointer z-20 transition-all hover:scale-110" id="prevBtn">
                <span class="material-icons text-white text-4xl">chevron_left</span>
            </button>
            <button class="absolute top-1/2 -translate-y-1/2 right-4 bg-white/20 hover:bg-white/40 backdrop-blur-sm border-none rounded-full w-14 h-14 flex items-center justify-center cursor-pointer z-20 transition-all hover:scale-110" id="nextBtn">
                <span class="material-icons text-white text-4xl">chevron_right</span>
            </button>

            <!-- Indicators -->
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-20" id="indicators">
                @foreach($banners as $index => $banner)
                <div class="indicator w-3 h-3 rounded-full bg-white/40 cursor-pointer hover:bg-white/60 transition-all {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></div>
                @endforeach
            </div>
            @endif
        @else
            <div class="flex flex-col items-center justify-center h-full text-white/70">
                <span class="material-icons text-8xl mb-4 opacity-40">image_not_supported</span>
                <p class="text-xl">Tiada event banner. Klik butang tambah untuk muat naik.</p>
            </div>
        @endif
    </div>

    <!-- Control buttons -->
    <div class="absolute top-6 right-6 flex gap-3 z-20">
        <button class="group bg-white/20 hover:bg-white/30 backdrop-blur-md border-none rounded-xl w-12 h-12 flex items-center justify-center cursor-pointer transition-all hover:scale-110" id="addBannerBtn" title="Tambah Banner">
            <span class="material-icons text-white text-2xl group-hover:scale-110 transition-transform">add</span>
        </button>
        @if($banners->count() > 0)
        <button class="group bg-white/20 hover:bg-white/30 backdrop-blur-md border-none rounded-xl w-12 h-12 flex items-center justify-center cursor-pointer transition-all hover:scale-110" id="editBannerBtn" title="Edit Banner">
            <span class="material-icons text-white text-2xl group-hover:scale-110 transition-transform">edit</span>
        </button>
        <button class="group bg-white/20 hover:bg-white/30 backdrop-blur-md border-none rounded-xl w-12 h-12 flex items-center justify-center cursor-pointer transition-all hover:scale-110" id="deleteBannerBtn" title="Padam Banner">
            <span class="material-icons text-white text-2xl group-hover:scale-110 transition-transform">delete</span>
        </button>
        @endif
    </div>
</div>

<!-- Banner Manager -->
@if($banners->count() > 0)
<div class="mb-8">
    <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
        <span class="material-icons text-3xl">collections</span>
        Semua Event Banners
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($banners as $banner)
        <div class="group bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all hover:-translate-y-1" data-banner-id="{{ $banner->id }}">
            <div class="relative h-40 overflow-hidden">
                <img src="{{ $banner->thumb_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
            <div class="p-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-1">{{ $banner->title }}</h4>
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $banner->description }}</p>
                <div class="flex gap-2">
                    <button class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors edit-banner-item" data-banner-id="{{ $banner->id }}">
                        <span class="material-icons text-sm">edit</span>
                        Edit
                    </button>
                    <button class="flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors delete-banner-item" data-banner-id="{{ $banner->id }}">
                        <span class="material-icons text-sm">delete</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Booking Summary Section -->
<div class="mb-8">
    <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
        <span class="material-icons text-3xl">assessment</span>
        Ringkasan Tempahan
    </h3>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Bookings -->
        <div class="group bg-gradient-to-br from-indigo-500 to-purple-600 p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-icons text-white text-3xl">event_note</span>
                </div>
                <span class="text-white/80 text-sm font-medium">Total</span>
            </div>
            <p class="text-4xl font-bold text-white mb-1">{{ $totalBookings }}</p>
            <p class="text-white/80 text-sm">Jumlah Tempahan</p>
        </div>

        <!-- Pending Bookings -->
        <div class="group bg-gradient-to-br from-pink-500 to-rose-600 p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-icons text-white text-3xl">pending_actions</span>
                </div>
                <span class="text-white/80 text-sm font-medium">Pending</span>
            </div>
            <p class="text-4xl font-bold text-white mb-1">{{ $pendingBookings }}</p>
            <p class="text-white/80 text-sm">Menunggu Kelulusan</p>
        </div>

        <!-- Approved Bookings -->
        <div class="group bg-gradient-to-br from-cyan-500 to-blue-600 p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-icons text-white text-3xl">check_circle</span>
                </div>
                <span class="text-white/80 text-sm font-medium">Approved</span>
            </div>
            <p class="text-4xl font-bold text-white mb-1">{{ $approvedBookings }}</p>
            <p class="text-white/80 text-sm">Diluluskan</p>
        </div>

        <!-- Today's Bookings -->
        <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 p-6 rounded-2xl shadow-xl hover:shadow-2xl transition-all hover:-translate-y-1 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-icons text-white text-3xl">today</span>
                </div>
                <span class="text-white/80 text-sm font-medium">Today</span>
            </div>
            <p class="text-4xl font-bold text-white mb-1">{{ $todayBookings }}</p>
            <p class="text-white/80 text-sm">Tempahan Hari Ini</p>
        </div>
    </div>

    <!-- Bookings Section -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Recent Bookings -->
        <div class="bg-white/95 backdrop-blur-sm p-6 rounded-2xl shadow-xl">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <span class="material-icons text-blue-600 text-2xl">schedule</span>
                <h4 class="text-xl font-bold text-gray-900">Tempahan Terkini</h4>
            </div>
            <div class="flex flex-col gap-3 max-h-[500px] overflow-y-auto pr-2">
                @forelse($recentBookings as $booking)
                <div class="group p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl border-l-4 hover:shadow-md transition-all hover:translate-x-1" style="border-left-color: {{ $booking->status_color }};">
                    <div class="flex justify-between items-start mb-3">
                        <h5 class="font-semibold text-gray-900 text-base">{{ $booking->user->name }}</h5>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold text-white" style="background-color: {{ $booking->status_color }};">
                            {{ $booking->status_label }}
                        </span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p class="flex items-center gap-2">
                            <span class="material-icons text-sm">directions_car</span>
                            <span class="font-medium">{{ $booking->vehicle_name }}</span>
                            <span class="text-gray-400">({{ $booking->vehicle_plate }})</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="material-icons text-sm">event</span>
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y H:i') }}
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="material-icons text-sm">place</span>
                            {{ $booking->destination }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400">
                    <span class="material-icons text-6xl mb-3 opacity-30">event_busy</span>
                    <p class="text-lg">Tiada tempahan terkini</p>
                </div>
                @endforelse
            </div>
            <a href="{{ route('admin.booking') }}" class="flex items-center justify-center gap-2 mt-5 py-3 text-blue-600 font-semibold rounded-xl transition-all hover:bg-blue-50 group">
                Lihat Semua Tempahan
                <span class="material-icons group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>

        <!-- Upcoming Bookings -->
        <div class="bg-white/95 backdrop-blur-sm p-6 rounded-2xl shadow-xl">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <span class="material-icons text-emerald-600 text-2xl">event_upcoming</span>
                <h4 class="text-xl font-bold text-gray-900">Tempahan Akan Datang</h4>
            </div>
            <div class="flex flex-col gap-3 max-h-[500px] overflow-y-auto pr-2">
                @forelse($upcomingBookings as $booking)
                <div class="group p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl border-l-4 hover:shadow-md transition-all hover:translate-x-1" style="border-left-color: {{ $booking->status_color }};">
                    <div class="flex justify-between items-start mb-3">
                        <h5 class="font-semibold text-gray-900 text-base">{{ $booking->user->name }}</h5>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold text-white" style="background-color: {{ $booking->status_color }};">
                            {{ $booking->status_label }}
                        </span>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p class="flex items-center gap-2">
                            <span class="material-icons text-sm">directions_car</span>
                            <span class="font-medium">{{ $booking->vehicle_name }}</span>
                            <span class="text-gray-400">({{ $booking->vehicle_plate }})</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="material-icons text-sm">event</span>
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y H:i') }}
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="material-icons text-sm">place</span>
                            {{ $booking->destination }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-12 text-gray-400">
                    <span class="material-icons text-6xl mb-3 opacity-30">event_available</span>
                    <p class="text-lg">Tiada tempahan akan datang</p>
                </div>
                @endforelse
            </div>
            <a href="{{ route('admin.booking') }}" class="flex items-center justify-center gap-2 mt-5 py-3 text-emerald-600 font-semibold rounded-xl transition-all hover:bg-emerald-50 group">
                Lihat Kalendar Tempahan
                <span class="material-icons group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </div>
    </div>
</div>

<!-- Add/Edit Banner Modal -->
<div class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm justify-center items-center z-[1000] p-4" id="bannerModal">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-2xl animate-fade-in">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-white">
            <h3 class="text-2xl font-bold flex items-center gap-3" id="modalTitle">
                <span class="material-icons text-3xl">add_photo_alternate</span>
                Tambah Event Banner
            </h3>
        </div>

        <form id="bannerForm" class="p-6 overflow-y-auto max-h-[calc(90vh-100px)]">
            <input type="hidden" id="bannerId" value="">

            <div class="mb-5">
                <label for="bannerTitle" class="block mb-2 text-gray-800 font-semibold flex items-center gap-2">
                    <span class="material-icons text-sm">title</span>
                    Tajuk Event *
                </label>
                <input type="text" id="bannerTitle" required placeholder="Contoh: Majlis Konvokesyen 2025" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none">
            </div>

            <div class="mb-5">
                <label for="bannerDescription" class="block mb-2 text-gray-800 font-semibold flex items-center gap-2">
                    <span class="material-icons text-sm">description</span>
                    Keterangan
                </label>
                <textarea id="bannerDescription" placeholder="Keterangan ringkas tentang event" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none resize-y min-h-[100px]"></textarea>
            </div>

            <div class="mb-5">
                <label class="block mb-2 text-gray-800 font-semibold flex items-center gap-2">
                    <span class="material-icons text-sm">image</span>
                    Gambar Banner *
                </label>
                <div id="dropZone" class="group border-3 border-dashed border-blue-400 p-10 rounded-xl cursor-pointer text-center bg-blue-50/50 hover:bg-blue-100/50 hover:border-blue-600 transition-all">
                    <span class="material-icons text-6xl text-blue-400 group-hover:text-blue-600 mb-3 block">cloud_upload</span>
                    <p class="text-blue-600 font-medium">Seret dan lepas gambar di sini</p>
                    <p class="text-gray-500 text-sm mt-2">atau klik untuk pilih fail</p>
                    <p class="text-gray-400 text-xs mt-2">Format: JPG, PNG (Max: 5MB)</p>
                    <input type="file" id="imgInput" accept="image/*" class="hidden">
                </div>
                <img id="preview" class="mt-4 w-full max-h-80 object-contain rounded-xl hidden shadow-lg" src="" alt="Preview">
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" class="flex-1 px-6 py-3 border-2 border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-all" id="cancelBtn">Batal</button>
                <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all" id="saveBtn">
                    <span class="flex items-center justify-center gap-2">
                        <span class="material-icons text-sm">save</span>
                        Simpan
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm justify-center items-center z-[1000] p-4" id="deleteModal">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden animate-fade-in">
        <div class="bg-gradient-to-r from-red-600 to-rose-600 p-6 text-white">
            <h3 class="text-2xl font-bold flex items-center gap-3">
                <span class="material-icons text-3xl">warning</span>
                Padam Banner
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 text-lg mb-6">Adakah anda pasti ingin memadam banner ini? Tindakan ini tidak boleh dibatalkan.</p>
            <div class="flex gap-3">
                <button type="button" class="flex-1 px-6 py-3 border-2 border-gray-300 rounded-xl font-semibold text-gray-700 hover:bg-gray-100 transition-all" id="cancelDeleteBtn">Batal</button>
                <button type="button" class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 text-white rounded-xl font-semibold hover:from-red-700 hover:to-rose-700 shadow-lg hover:shadow-xl transition-all" id="confirmDeleteBtn">
                    <span class="flex items-center justify-center gap-2">
                        <span class="material-icons text-sm">delete</span>
                        Padam
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    // State
    let currentSlide = 0;
    let cropper = null;
    let selectedFile = null;
    let editingBannerId = null;
    const csrfToken = '{{ csrf_token() }}';

    // Elements
    const bannerModal = document.getElementById('bannerModal');
    const deleteModal = document.getElementById('deleteModal');
    const bannerForm = document.getElementById('bannerForm');
    const imgInput = document.getElementById('imgInput');
    const dropZone = document.getElementById('dropZone');
    const preview = document.getElementById('preview');
    const slides = document.querySelectorAll('.banner-slide');
    const indicators = document.querySelectorAll('.indicator');
    const totalSlides = slides.length;

    // Carousel functions
    function showSlide(n) {
        if (totalSlides === 0) return;

        currentSlide = (n + totalSlides) % totalSlides;

        slides.forEach((slide, index) => {
            slide.classList.toggle('active', index === currentSlide);
        });

        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentSlide);
        });
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    // Auto-advance carousel every 5 seconds
    let autoSlide = setInterval(nextSlide, 5000);

    // Event listeners for carousel
    document.getElementById('nextBtn')?.addEventListener('click', () => {
        nextSlide();
        clearInterval(autoSlide);
        autoSlide = setInterval(nextSlide, 5000);
    });

    document.getElementById('prevBtn')?.addEventListener('click', () => {
        prevSlide();
        clearInterval(autoSlide);
        autoSlide = setInterval(nextSlide, 5000);
    });

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            showSlide(index);
            clearInterval(autoSlide);
            autoSlide = setInterval(nextSlide, 5000);
        });
    });

    // Add banner button
    document.getElementById('addBannerBtn').addEventListener('click', () => {
        editingBannerId = null;
        document.getElementById('modalTitle').textContent = 'Tambah Event Banner';
        document.getElementById('bannerId').value = '';
        document.getElementById('bannerTitle').value = '';
        document.getElementById('bannerDescription').value = '';
        preview.src = '';
        preview.classList.remove('block');
        preview.classList.add('hidden');
        selectedFile = null;
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        bannerModal.classList.remove('hidden');
        bannerModal.classList.add('flex');
    });

    // Edit current banner button
    document.getElementById('editBannerBtn')?.addEventListener('click', () => {
        const activeSlide = document.querySelector('.banner-slide.active');
        if (activeSlide) {
            const bannerId = activeSlide.dataset.bannerId;
            editBanner(bannerId);
        }
    });

    // Delete current banner button
    document.getElementById('deleteBannerBtn')?.addEventListener('click', () => {
        const activeSlide = document.querySelector('.banner-slide.active');
        if (activeSlide) {
            editingBannerId = activeSlide.dataset.bannerId;
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        }
    });

    // Edit banner from list
    document.querySelectorAll('.edit-banner-item').forEach(btn => {
        btn.addEventListener('click', () => {
            const bannerId = btn.dataset.bannerId;
            editBanner(bannerId);
        });
    });

    // Delete banner from list
    document.querySelectorAll('.delete-banner-item').forEach(btn => {
        btn.addEventListener('click', () => {
            editingBannerId = btn.dataset.bannerId;
            deleteModal.style.display = 'flex';
        });
    });

    // Edit banner function
    function editBanner(bannerId) {
        const bannerSlide = document.querySelector(`.banner-slide[data-banner-id="${bannerId}"]`);
        if (!bannerSlide) return;

        editingBannerId = bannerId;
        document.getElementById('modalTitle').textContent = 'Edit Event Banner';
        document.getElementById('bannerId').value = bannerId;

        const title = bannerSlide.querySelector('h2').textContent;
        const description = bannerSlide.querySelector('p').textContent;

        document.getElementById('bannerTitle').value = title;
        document.getElementById('bannerDescription').value = description;

        preview.src = '';
        preview.classList.remove('block');
        preview.classList.add('hidden');
        selectedFile = null;

        if (cropper) {
            cropper.destroy();
            cropper = null;
        }

        bannerModal.classList.remove('hidden');
        bannerModal.classList.add('flex');
    }

    // File input and drag-drop
    dropZone.addEventListener('click', () => imgInput.click());

    imgInput.addEventListener('change', (e) => {
        if (e.target.files[0]) {
            handleFile(e.target.files[0]);
        }
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('bg-blue-50');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('bg-blue-50');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-blue-50');
        if (e.dataTransfer.files[0]) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    function handleFile(file) {
        if (!file.type.startsWith('image/')) {
            alert('Sila pilih fail gambar sahaja');
            return;
        }

        selectedFile = file;
        const reader = new FileReader();

        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            preview.classList.add('block');

            setTimeout(() => {
                if (cropper) cropper.destroy();
                cropper = new Cropper(preview, {
                    aspectRatio: 16 / 9,
                    viewMode: 1,
                    autoCropArea: 1,
                    movable: true,
                    zoomable: true,
                    rotatable: true,
                    scalable: true,
                });
            }, 100);
        };

        reader.readAsDataURL(file);
    }

    // Form submission
    bannerForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const title = document.getElementById('bannerTitle').value;
        const description = document.getElementById('bannerDescription').value;
        const bannerId = document.getElementById('bannerId').value;

        // Validate
        if (!title) {
            alert('Sila masukkan tajuk event');
            return;
        }

        let imageData = null;

        // Get cropped image if available
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({ width: 1280, height: 720 });
            imageData = canvas.toDataURL('image/jpeg', 0.9);
        }

        // For new banner, image is required
        if (!bannerId && !imageData) {
            alert('Sila pilih gambar untuk banner');
            return;
        }

        const payload = {
            title,
            description,
            image: imageData,
        };

        try {
            const url = bannerId
                ? `/admin/banners/${bannerId}`
                : '/admin/banners';

            const method = bannerId ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert('Ralat: ' + (result.message || 'Gagal menyimpan banner'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ralat semasa menyimpan banner');
        }
    });

    // Delete confirmation
    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
        if (!editingBannerId) return;

        try {
            const response = await fetch(`/admin/banners/${editingBannerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert('Ralat: ' + (result.message || 'Gagal memadam banner'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ralat semasa memadam banner');
        }
    });

    // Cancel buttons
    document.getElementById('cancelBtn').addEventListener('click', () => {
        bannerModal.classList.add('hidden');
        bannerModal.classList.remove('flex');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
        editingBannerId = null;
    });

    // Close modals on outside click
    bannerModal.addEventListener('click', (e) => {
        if (e.target === bannerModal) {
            bannerModal.classList.add('hidden');
            bannerModal.classList.remove('flex');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }
    });

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
            editingBannerId = null;
        }
    });
</script>
@endpush
