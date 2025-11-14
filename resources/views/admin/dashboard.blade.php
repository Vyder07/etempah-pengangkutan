@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
    /* Banner / upload */
    .banner-container {
        position: relative; width:100%; height:320px; overflow:hidden; border-radius:12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15); background:#d1d5db; display:flex;justify-content:center;align-items:center;
    }
    .banner-container img { width:100%; height:100%; object-fit:cover; filter:brightness(0.8); transition:opacity .3s ease; }

    .banner-text {
        position:absolute; bottom:20px; left:30px; color:#fff; display:flex; align-items:center; gap:8px;
        z-index:5;
    }
    .banner-text h2{ margin:0; font-size:1.8em; font-weight:600; text-shadow:0 2px 6px rgba(0,0,0,0.4); }
    .banner-text p{ margin-top:6px; font-size:1em; opacity:0.9; }

    .upload-overlay {
        position:absolute; inset:0; display:flex; justify-content:center; align-items:center;
        background: rgba(0,0,0,0.5); opacity:0; transition:opacity .3s ease; cursor: default;
        z-index:3;
        pointer-events: none;
    }
    .upload-overlay .material-icons {
        font-size:64px; color:#fff; background: rgba(255,255,255,0.15); padding:20px; border-radius:50%;
        transition: transform .2s ease;
        pointer-events: auto;
    }
    .banner-container:hover .upload-overlay { opacity:1; }
    .upload-overlay:hover .material-icons { transform:scale(1.1); }

    .edit-icon {
        background: rgba(255,255,255,0.3); border-radius:50%; padding:4px; cursor:pointer;
        transition: background .2s; z-index:6; position:relative;
    }
    .edit-icon:hover{ background: rgba(255,255,255,0.5); }

    .event-details { margin-top:15px; text-align:center; color:#333; font-size:1.05rem; }
    .event-details span{ font-weight:600; }

    .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); justify-content:center; align-items:center; z-index:1000; }
    .modal-content { background:#fff; padding:20px 25px; border-radius:12px; width:350px; text-align:center; box-shadow:0 4px 10px rgba(0,0,0,0.3); }
    .modal-content input, .modal-content textarea { width:100%; margin-top:10px; padding:8px; border:1px solid #ccc; border-radius:8px; }
    .modal-content button { margin-top:15px; background:#2563eb; color:#fff; border:none; border-radius:8px; padding:8px 14px; cursor:pointer; }
    .preview-img { margin-top:10px; width:100%; height:150px; object-fit:cover; border-radius:8px; }
</style>
@endpush

@section('content')
<div class="kotak">
    <div class="banner-container" id="banner">
        <img id="bannerImg" src="https://via.placeholder.com/900x300?text=Tiada+Event+Terbaharu" alt="Event Banner" />

        <div class="upload-overlay" id="uploadOverlay">
            <span class="material-icons" id="uploadIcon">upload</span>
        </div>

        <div class="banner-text">
            <h2 id="eventTitle">Tiada Event Terbaharu</h2>
            <span class="material-icons edit-icon" id="editTextBtn">edit</span>
        </div>
    </div>

    <div class="event-details">
        <p id="eventCaption">Tiada event semasa. Nantikan kemas kini akan datang.</p>
    </div>
</div>

<!-- Text modal -->
<div class="modal" id="textModal">
    <div class="modal-content">
        <h3>Edit Caption Event</h3>
        <input type="text" id="editTitle" placeholder="Tajuk event">
        <textarea id="editDesc" rows="3" placeholder="Keterangan event"></textarea>
        <button id="saveTextBtn">Simpan</button>
    </div>
</div>

<!-- Image modal -->
<div class="modal" id="imgModal">
    <div class="modal-content">
        <h3>Muat Naik Gambar Event</h3>
        <div id="dropZone" style="border:2px dashed #2563eb; padding:30px; border-radius:10px; cursor:pointer;">
            Seret dan lepas gambar di sini atau klik untuk pilih fail
            <input type="file" id="imgInput" accept="image/*" style="display:none;">
        </div>
        <img id="preview" class="preview-img" src="" alt="">
        <button id="saveImgBtn">Simpan</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    const isAdmin = true;

    const uploadOverlay = document.getElementById('uploadOverlay');
    const uploadIcon = document.getElementById('uploadIcon');
    const bannerImg = document.getElementById('bannerImg');
    const eventTitle = document.getElementById('eventTitle');
    const eventCaption = document.getElementById('eventCaption');

    const textModal = document.getElementById('textModal');
    const imgModal = document.getElementById('imgModal');
    const saveTextBtn = document.getElementById('saveTextBtn');
    const saveImgBtn = document.getElementById('saveImgBtn');
    const editTextBtn = document.getElementById('editTextBtn');
    const imgInput = document.getElementById('imgInput');
    const dropZone = document.getElementById('dropZone');
    const previewImg = document.getElementById('preview');

    if (!isAdmin) uploadOverlay.style.display = 'none';

    /* ---------- Edit text ---------- */
    editTextBtn.addEventListener('click', () => {
        document.getElementById('editTitle').value = eventTitle.textContent;
        document.getElementById('editDesc').value = eventCaption.textContent;
        textModal.style.display = 'flex';
    });
    saveTextBtn.addEventListener('click', () => {
        eventTitle.textContent = document.getElementById('editTitle').value || 'Tiada Event Terbaharu';
        eventCaption.textContent = document.getElementById('editDesc').value || 'Tiada event semasa. Nantikan kemas kini akan datang.';
        textModal.style.display = 'none';
    });

    /* ---------- Upload & Crop ---------- */
    let cropper = null;

    function openCropModal(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            imgModal.style.display = 'flex';
            setTimeout(() => {
                if (cropper) cropper.destroy();
                cropper = new Cropper(previewImg, {
                    aspectRatio: 16 / 9,
                    viewMode: 1,
                    autoCropArea: 1,
                    movable: true,
                    zoomable: true,
                    rotatable: false,
                    scalable: false,
                });
            }, 300);
        };
        reader.readAsDataURL(file);
    }

    // open upload modal
    if (uploadIcon) {
        uploadIcon.addEventListener('click', (e) => {
            e.stopPropagation();
            imgInput.click();
        });
    }

    // select file
    imgInput.addEventListener('change', (e) => openCropModal(e.target.files[0]));

    // drag drop area
    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.background = '#e0e7ff'; });
    dropZone.addEventListener('dragleave', () => dropZone.style.background = 'transparent');
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.style.background = 'transparent';
        openCropModal(e.dataTransfer.files[0]);
    });

    // save cropped
    saveImgBtn.addEventListener('click', () => {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({ width: 1280, height: 720 });
            bannerImg.src = canvas.toDataURL('image/jpeg');
            cropper.destroy();
            cropper = null;
        }
        imgModal.style.display = 'none';
    });

    // close modal
    imgModal.addEventListener('click', e => {
        if (e.target === imgModal) {
            imgModal.style.display = 'none';
            if (cropper) { cropper.destroy(); cropper = null; }
        }
    });
    textModal.addEventListener('click', e => { if (e.target === textModal) textModal.style.display = 'none'; });
</script>
@endpush
