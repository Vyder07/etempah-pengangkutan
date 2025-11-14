@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
    /* Only keeping styles that can't be easily done with Tailwind */
    .banner-slide {
        display: none;
    }
    .banner-slide.active {
        display: block;
    }
    .indicator {
        transition: all 0.3s;
    }
    .indicator.active {
        background: #fff;
        width: 30px;
        border-radius: 5px;
    }
    .summary-value {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        margin: 5px 0 0 0;
    }
    .booking-item-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 8px;
    }
    .booking-item-header h5 {
        font-weight: 600;
        color: #111827;
        font-size: 15px;
        margin: 0;
    }
    .booking-status {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        color: white;
    }
    .booking-item-details {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.6;
        margin: 4px 0;
    }
</style>
@endpush

@section('content')
<div class="kotak">
    <!-- Banner Carousel -->
    <div class="relative w-full h-80 overflow-hidden rounded-xl shadow-lg bg-gray-300">
        <div class="relative w-full h-full" id="bannerCarousel">
            @if($banners->count() > 0)
                @foreach($banners as $index => $banner)
                <div class="banner-slide {{ $index === 0 ? 'active' : '' }} relative w-full h-full" data-banner-id="{{ $banner->id }}">
                    <img src="{{ $banner->banner_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover brightness-75">
                    <div class="absolute bottom-5 left-7 text-white z-10 max-w-[70%]">
                        <h2 class="text-3xl font-semibold m-0 drop-shadow-lg">{{ $banner->title }}</h2>
                        <p class="mt-1.5 text-base opacity-90">{{ $banner->description }}</p>
                    </div>
                </div>
                @endforeach

                <!-- Navigation arrows -->
                @if($banners->count() > 1)
                <button class="absolute top-1/2 -translate-y-1/2 left-5 bg-black/50 hover:bg-black/70 border-none rounded-full w-12 h-12 flex items-center justify-center cursor-pointer z-10 transition-all" id="prevBtn">
                    <span class="material-icons text-white text-3xl">chevron_left</span>
                </button>
                <button class="absolute top-1/2 -translate-y-1/2 right-5 bg-black/50 hover:bg-black/70 border-none rounded-full w-12 h-12 flex items-center justify-center cursor-pointer z-10 transition-all" id="nextBtn">
                    <span class="material-icons text-white text-3xl">chevron_right</span>
                </button>

                <!-- Indicators -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10" id="indicators">
                    @foreach($banners as $index => $banner)
                    <div class="indicator w-2.5 h-2.5 rounded-full bg-white/50 cursor-pointer {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></div>
                    @endforeach
                </div>
                @endif
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-600">
                    <span class="material-icons text-6xl mb-4 opacity-50">image_not_supported</span>
                    <p>Tiada event banner. Klik butang tambah untuk muat naik.</p>
                </div>
            @endif
        </div>

        <!-- Control buttons -->
        <div class="absolute top-5 right-5 flex gap-2.5 z-10">
            <button class="bg-white/30 backdrop-blur-md border-none rounded-full w-10 h-10 flex items-center justify-center cursor-pointer transition-all hover:bg-white/50 hover:scale-110" id="addBannerBtn" title="Tambah Banner">
                <span class="material-icons text-white text-2xl">add</span>
            </button>
            @if($banners->count() > 0)
            <button class="bg-white/30 backdrop-blur-md border-none rounded-full w-10 h-10 flex items-center justify-center cursor-pointer transition-all hover:bg-white/50 hover:scale-110" id="editBannerBtn" title="Edit Banner">
                <span class="material-icons text-white text-2xl">edit</span>
            </button>
            <button class="bg-white/30 backdrop-blur-md border-none rounded-full w-10 h-10 flex items-center justify-center cursor-pointer transition-all hover:bg-white/50 hover:scale-110" id="deleteBannerBtn" title="Padam Banner">
                <span class="material-icons text-white text-2xl">delete</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Banner Manager (Optional - shows all banners) -->
    @if($banners->count() > 0)
    <div class="mt-8">
        <h3 class="mb-4 text-lg font-semibold">Semua Event Banners</h3>
        @foreach($banners as $banner)
        <div class="flex gap-4 p-4 bg-gray-50 rounded-lg mb-2.5 items-center" data-banner-id="{{ $banner->id }}">
            <img src="{{ $banner->thumb_url }}" alt="{{ $banner->title }}" class="w-30 h-[70px] object-cover rounded-md">
            <div class="flex-1">
                <h4 class="m-0 mb-1 text-gray-900">{{ $banner->title }}</h4>
                <p class="m-0 text-gray-600 text-sm">{{ Str::limit($banner->description, 80) }}</p>
            </div>
            <div class="flex gap-2">
                <button class="bg-transparent border-none cursor-pointer p-2 rounded-full transition-all hover:bg-black/10 edit-banner-item" data-banner-id="{{ $banner->id }}" title="Edit">
                    <span class="material-icons text-xl text-gray-600">edit</span>
                </button>
                <button class="bg-transparent border-none cursor-pointer p-2 rounded-full transition-all hover:bg-black/10 delete-banner-item" data-banner-id="{{ $banner->id }}" title="Padam">
                    <span class="material-icons text-xl text-gray-600 hover:text-red-600">delete</span>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Booking Summary Section -->
    <div class="mt-8">
        <h3 class="mb-5 text-2xl font-semibold text-gray-900">Ringkasan Tempahan</h3>

        <!-- Summary Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Bookings -->
            <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4 transition-all hover:-translate-y-0.5 hover:shadow-lg">
                <div class="w-15 h-15 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <span class="material-icons text-white text-3xl">event_note</span>
                </div>
                <div>
                    <h4 class="m-0 text-sm text-gray-500 font-medium">Jumlah Tempahan</h4>
                    <p class="summary-value">{{ $totalBookings }}</p>
                </div>
            </div>

            <!-- Pending Bookings -->
            <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4 transition-all hover:-translate-y-0.5 hover:shadow-lg">
                <div class="w-15 h-15 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <span class="material-icons text-white text-3xl">pending_actions</span>
                </div>
                <div>
                    <h4 class="m-0 text-sm text-gray-500 font-medium">Menunggu Kelulusan</h4>
                    <p class="summary-value">{{ $pendingBookings }}</p>
                </div>
            </div>

            <!-- Approved Bookings -->
            <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4 transition-all hover:-translate-y-0.5 hover:shadow-lg">
                <div class="w-15 h-15 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <span class="material-icons text-white text-3xl">check_circle</span>
                </div>
                <div>
                    <h4 class="m-0 text-sm text-gray-500 font-medium">Diluluskan</h4>
                    <p class="summary-value">{{ $approvedBookings }}</p>
                </div>
            </div>

            <!-- Today's Bookings -->
            <div class="bg-white p-5 rounded-xl shadow-md flex items-center gap-4 transition-all hover:-translate-y-0.5 hover:shadow-lg">
                <div class="w-15 h-15 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <span class="material-icons text-white text-3xl">today</span>
                </div>
                <div>
                    <h4 class="m-0 text-sm text-gray-500 font-medium">Tempahan Hari Ini</h4>
                    <p class="summary-value">{{ $todayBookings }}</p>
                </div>
            </div>
        </div>

        <!-- Bookings Section -->
        <div class="grid md:grid-cols-2 gap-5">
            <!-- Recent Bookings -->
            <div class="bg-white p-5 rounded-xl shadow-md">
                <h4 class="m-0 mb-5 text-xl text-gray-900">Tempahan Terkini</h4>
                <div class="flex flex-col gap-3">
                    @forelse($recentBookings as $booking)
                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 transition-all hover:bg-gray-100 hover:translate-x-0.5" style="border-left-color: {{ $booking->status_color }};">
                        <div class="booking-item-header">
                            <h5>{{ $booking->user->name }}</h5>
                            <span class="booking-status" style="background-color: {{ $booking->status_color }};">
                                {{ $booking->status_label }}
                            </span>
                        </div>
                        <p class="booking-item-details">
                            <span class="material-icons text-base align-middle">directions_car</span>
                            {{ $booking->vehicle_name }} ({{ $booking->vehicle_plate }})
                        </p>
                        <p class="booking-item-details">
                            <span class="material-icons text-base align-middle">event</span>
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y H:i') }}
                        </p>
                        <p class="booking-item-details">
                            <span class="material-icons text-base align-middle">place</span>
                            {{ $booking->destination }}
                        </p>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-icons text-5xl mb-2.5 opacity-50">event_busy</span>
                        <p>Tiada tempahan terkini</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ route('admin.booking') }}" class="block text-center mt-4 py-2.5 text-blue-600 no-underline font-medium rounded-md transition-all hover:bg-blue-50">
                    Lihat Semua Tempahan
                    <span class="material-icons text-base align-middle">arrow_forward</span>
                </a>
            </div>

            <!-- Upcoming Bookings -->
            <div class="bg-white p-5 rounded-xl shadow-md">
                <h4 class="m-0 mb-5 text-xl text-gray-900">Tempahan Akan Datang</h4>
                <div class="flex flex-col gap-3">
                    @forelse($upcomingBookings as $booking)
                    <div class="p-4 bg-gray-50 rounded-lg border-l-4 transition-all hover:bg-gray-100 hover:translate-x-0.5" style="border-left-color: {{ $booking->status_color }};">
                        <div class="booking-item-header">
                            <h5>{{ $booking->user->name }}</h5>
                            <span class="booking-status" style="background-color: {{ $booking->status_color }};">
                                {{ $booking->status_label }}
                            </span>
                        </div>
                        <p class="booking-item-details">
                            <span class="material-icons text-base align-middle">directions_car</span>
                            {{ $booking->vehicle_name }} ({{ $booking->vehicle_plate }})
                        </p>
                        <p class="booking-item-details">
                            <span class="material-icons text-base align-middle">event</span>
                            {{ \Carbon\Carbon::parse($booking->start_date)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($booking->end_date)->format('d/m/Y H:i') }}
                        </p>
                        <p class="booking-item-details">
                            <span class="material-icons text-base align-middle">place</span>
                            {{ $booking->destination }}
                        </p>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-400">
                        <span class="material-icons text-5xl mb-2.5 opacity-50">event_available</span>
                        <p>Tiada tempahan akan datang yang diluluskan</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ route('admin.booking') }}" class="block text-center mt-4 py-2.5 text-blue-600 no-underline font-medium rounded-md transition-all hover:bg-blue-50">
                    Lihat Kalendar Tempahan
                    <span class="material-icons text-base align-middle">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Banner Modal -->
<div class="hidden fixed inset-0 bg-black/50 justify-center items-center z-[1000]" id="bannerModal">
    <div class="bg-white p-6 rounded-xl w-[90%] max-w-lg max-h-[90vh] overflow-y-auto shadow-xl">
        <h3 class="mt-0 mb-5 text-gray-900" id="modalTitle">Tambah Event Banner</h3>
        <form id="bannerForm">
            <input type="hidden" id="bannerId" value="">

            <div class="mb-4">
                <label for="bannerTitle" class="block mb-1.5 text-gray-700 font-medium">Tajuk Event *</label>
                <input type="text" id="bannerTitle" required placeholder="Contoh: Majlis Konvokesyen 2025" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>

            <div class="mb-4">
                <label for="bannerDescription" class="block mb-1.5 text-gray-700 font-medium">Keterangan</label>
                <textarea id="bannerDescription" placeholder="Keterangan ringkas tentang event" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm resize-y min-h-[80px]"></textarea>
            </div>

            <div class="mb-4">
                <label class="block mb-1.5 text-gray-700 font-medium">Gambar Banner *</label>
                <div id="dropZone" class="border-2 border-dashed border-blue-600 p-8 rounded-lg cursor-pointer text-center text-blue-600 transition-all hover:bg-blue-50">
                    <p>Seret dan lepas gambar di sini atau klik untuk pilih fail</p>
                    <input type="file" id="imgInput" accept="image/*" class="hidden">
                </div>
                <img id="preview" class="mt-4 w-full max-h-[300px] object-contain rounded-lg hidden" src="" alt="Preview">
            </div>

            <div class="flex gap-2.5 mt-5">
                <button type="button" class="flex-1 px-5 py-2.5 border-none rounded-lg cursor-pointer text-sm font-medium bg-gray-600 text-white transition-all hover:bg-gray-700" id="cancelBtn">Batal</button>
                <button type="submit" class="flex-1 px-5 py-2.5 border-none rounded-lg cursor-pointer text-sm font-medium bg-blue-600 text-white transition-all hover:bg-blue-700" id="saveBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="hidden fixed inset-0 bg-black/50 justify-center items-center z-[1000]" id="deleteModal">
    <div class="bg-white p-6 rounded-xl max-w-sm shadow-xl">
        <h3 class="mt-0 mb-4 text-gray-900">Padam Banner</h3>
        <p class="mb-5 text-gray-600">Adakah anda pasti ingin memadam banner ini?</p>
        <div class="flex gap-2.5">
            <button type="button" class="flex-1 px-5 py-2.5 border-none rounded-lg cursor-pointer text-sm font-medium bg-gray-600 text-white transition-all hover:bg-gray-700" id="cancelDeleteBtn">Batal</button>
            <button type="button" class="flex-1 px-5 py-2.5 border-none rounded-lg cursor-pointer text-sm font-medium bg-red-600 text-white transition-all hover:bg-red-700" id="confirmDeleteBtn">Padam</button>
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
