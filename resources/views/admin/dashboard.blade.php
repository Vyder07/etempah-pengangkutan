@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
    /* Banner carousel container */
    .banner-carousel-wrapper {
        position: relative;
        width: 100%;
        height: 320px;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        background: #d1d5db;
    }

    .banner-carousel {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .banner-slide {
        display: none;
        position: relative;
        width: 100%;
        height: 100%;
    }

    .banner-slide.active {
        display: block;
    }

    .banner-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.8);
    }

    .banner-text {
        position: absolute;
        bottom: 20px;
        left: 30px;
        color: #fff;
        z-index: 5;
        max-width: 70%;
    }

    .banner-text h2 {
        margin: 0;
        font-size: 1.8em;
        font-weight: 600;
        text-shadow: 0 2px 6px rgba(0,0,0,0.4);
    }

    .banner-text p {
        margin-top: 6px;
        font-size: 1em;
        opacity: 0.9;
    }

    /* Banner controls */
    .banner-controls {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        gap: 10px;
        z-index: 10;
    }

    .banner-btn {
        background: rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .banner-btn:hover {
        background: rgba(255,255,255,0.5);
        transform: scale(1.1);
    }

    .banner-btn .material-icons {
        color: #fff;
        font-size: 24px;
    }

    /* Navigation arrows */
    .carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s;
    }

    .carousel-nav:hover {
        background: rgba(0,0,0,0.7);
    }

    .carousel-nav.prev {
        left: 20px;
    }

    .carousel-nav.next {
        right: 20px;
    }

    .carousel-nav .material-icons {
        color: #fff;
        font-size: 32px;
    }

    /* Carousel indicators */
    .carousel-indicators {
        position: absolute;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 10;
    }

    .indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.3s;
    }

    .indicator.active {
        background: #fff;
        width: 30px;
        border-radius: 5px;
    }

    /* Empty state */
    .empty-banner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #666;
    }

    .empty-banner .material-icons {
        font-size: 64px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    /* Modal styles */
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .modal-content h3 {
        margin-top: 0;
        margin-bottom: 20px;
        color: #333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: #555;
        font-weight: 500;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    #dropZone {
        border: 2px dashed #2563eb;
        padding: 30px;
        border-radius: 10px;
        cursor: pointer;
        text-align: center;
        color: #2563eb;
        transition: all 0.3s;
    }

    #dropZone:hover,
    #dropZone.drag-over {
        background: #e0e7ff;
        border-color: #1d4ed8;
    }

    .preview-img {
        margin-top: 15px;
        width: 100%;
        max-height: 300px;
        object-fit: contain;
        border-radius: 8px;
        display: none;
    }

    .preview-img.show {
        display: block;
    }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .btn {
        flex: 1;
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-secondary {
        background: #6b7280;
        color: #fff;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    .btn-danger {
        background: #dc2626;
        color: #fff;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    /* Banner manager list */
    .banner-list {
        margin-top: 30px;
    }

    .banner-item {
        display: flex;
        gap: 15px;
        padding: 15px;
        background: #f9fafb;
        border-radius: 8px;
        margin-bottom: 10px;
        align-items: center;
    }

    .banner-item img {
        width: 120px;
        height: 70px;
        object-fit: cover;
        border-radius: 6px;
    }

    .banner-item-info {
        flex: 1;
    }

    .banner-item-info h4 {
        margin: 0 0 5px 0;
        color: #333;
    }

    .banner-item-info p {
        margin: 0;
        color: #666;
        font-size: 14px;
    }

    .banner-item-actions {
        display: flex;
        gap: 8px;
    }

    .icon-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .icon-btn:hover {
        background: rgba(0,0,0,0.1);
    }

    .icon-btn .material-icons {
        font-size: 20px;
        color: #666;
    }

    .icon-btn.delete:hover .material-icons {
        color: #dc2626;
    }
</style>
@endpush

@section('content')
<div class="kotak">
    <!-- Banner Carousel -->
    <div class="banner-carousel-wrapper">
        <div class="banner-carousel" id="bannerCarousel">
            @if($banners->count() > 0)
                @foreach($banners as $index => $banner)
                <div class="banner-slide {{ $index === 0 ? 'active' : '' }}" data-banner-id="{{ $banner->id }}">
                    <img src="{{ $banner->banner_url }}" alt="{{ $banner->title }}">
                    <div class="banner-text">
                        <h2>{{ $banner->title }}</h2>
                        <p>{{ $banner->description }}</p>
                    </div>
                </div>
                @endforeach

                <!-- Navigation arrows -->
                @if($banners->count() > 1)
                <button class="carousel-nav prev" id="prevBtn">
                    <span class="material-icons">chevron_left</span>
                </button>
                <button class="carousel-nav next" id="nextBtn">
                    <span class="material-icons">chevron_right</span>
                </button>

                <!-- Indicators -->
                <div class="carousel-indicators" id="indicators">
                    @foreach($banners as $index => $banner)
                    <div class="indicator {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></div>
                    @endforeach
                </div>
                @endif
            @else
                <div class="empty-banner">
                    <span class="material-icons">image_not_supported</span>
                    <p>Tiada event banner. Klik butang tambah untuk muat naik.</p>
                </div>
            @endif
        </div>

        <!-- Control buttons -->
        <div class="banner-controls">
            <button class="banner-btn" id="addBannerBtn" title="Tambah Banner">
                <span class="material-icons">add</span>
            </button>
            @if($banners->count() > 0)
            <button class="banner-btn" id="editBannerBtn" title="Edit Banner">
                <span class="material-icons">edit</span>
            </button>
            <button class="banner-btn" id="deleteBannerBtn" title="Padam Banner">
                <span class="material-icons">delete</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Banner Manager (Optional - shows all banners) -->
    @if($banners->count() > 0)
    <div class="banner-list">
        <h3 style="margin-bottom: 15px;">Semua Event Banners</h3>
        @foreach($banners as $banner)
        <div class="banner-item" data-banner-id="{{ $banner->id }}">
            <img src="{{ $banner->thumb_url }}" alt="{{ $banner->title }}">
            <div class="banner-item-info">
                <h4>{{ $banner->title }}</h4>
                <p>{{ Str::limit($banner->description, 80) }}</p>
            </div>
            <div class="banner-item-actions">
                <button class="icon-btn edit-banner-item" data-banner-id="{{ $banner->id }}" title="Edit">
                    <span class="material-icons">edit</span>
                </button>
                <button class="icon-btn delete delete-banner-item" data-banner-id="{{ $banner->id }}" title="Padam">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<!-- Add/Edit Banner Modal -->
<div class="modal" id="bannerModal">
    <div class="modal-content">
        <h3 id="modalTitle">Tambah Event Banner</h3>
        <form id="bannerForm">
            <input type="hidden" id="bannerId" value="">
            
            <div class="form-group">
                <label for="bannerTitle">Tajuk Event *</label>
                <input type="text" id="bannerTitle" required placeholder="Contoh: Majlis Konvokesyen 2025">
            </div>

            <div class="form-group">
                <label for="bannerDescription">Keterangan</label>
                <textarea id="bannerDescription" placeholder="Keterangan ringkas tentang event"></textarea>
            </div>

            <div class="form-group">
                <label>Gambar Banner *</label>
                <div id="dropZone">
                    <p>Seret dan lepas gambar di sini atau klik untuk pilih fail</p>
                    <input type="file" id="imgInput" accept="image/*" style="display:none;">
                </div>
                <img id="preview" class="preview-img" src="" alt="Preview">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" id="cancelBtn">Batal</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-content" style="max-width: 400px;">
        <h3>Padam Banner</h3>
        <p>Adakah anda pasti ingin memadam banner ini?</p>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" id="cancelDeleteBtn">Batal</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Padam</button>
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
        preview.classList.remove('show');
        selectedFile = null;
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        bannerModal.style.display = 'flex';
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
            deleteModal.style.display = 'flex';
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
        preview.classList.remove('show');
        selectedFile = null;
        
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        
        bannerModal.style.display = 'flex';
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
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
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
            preview.classList.add('show');
            
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
        bannerModal.style.display = 'none';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    });

    document.getElementById('cancelDeleteBtn').addEventListener('click', () => {
        deleteModal.style.display = 'none';
        editingBannerId = null;
    });

    // Close modals on outside click
    bannerModal.addEventListener('click', (e) => {
        if (e.target === bannerModal) {
            bannerModal.style.display = 'none';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        }
    });

    deleteModal.addEventListener('click', (e) => {
        if (e.target === deleteModal) {
            deleteModal.style.display = 'none';
            editingBannerId = null;
        }
    });
</script>
@endpush
