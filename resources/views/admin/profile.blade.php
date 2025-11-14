@extends('admin.layouts.app')

@section('title', 'Profil Admin')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
    .content {margin-left:260px; margin-top:84px; display:flex; justify-content:center; align-items:flex-start; gap:30px; padding:20px; flex-wrap:wrap;}
    .card {background:rgba(255,255,255,0.95); border-radius:15px; box-shadow:0px 4px 10px rgba(0,0,0,0.2); padding:20px;}
    .card.profile {width:340px; text-align:center;}
    .profile-container {position:relative; display:inline-block; margin-bottom:10px;}
    .profile-container img {width:120px; height:120px; border-radius:50%; object-fit:cover; background:#eee; cursor:default; border:3px solid #0c48fe;}
    .upload-btn {position:absolute; bottom:0; right:0; background:rgb(9,30,255); color:white; border-radius:50%; padding:6px; font-size:14px; cursor:pointer;}
    input[type="file"] {display:none;}
    .role {color:red; font-weight:bold; margin-bottom:15px;}
    label {font-weight:bold; display:block; margin-top:10px; text-align:left;}
    input {width:100%; padding:8px; border-radius:8px; border:1px solid #ccc; background:#eee;}
    .card.profile button {margin-top:15px; width:100%; padding:10px; background:#0c48fe; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:bold;}
    .card.profile button:hover {background:#0734bb;}
    .card.project {width:500px; text-align:left;}
    .project-header {display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;}
    .project-header img {max-height:50px;}

    #cropModal {display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:9999;}
    #cropBox {background:white; padding:20px; border-radius:10px; max-width:500px; text-align:center;}
    #cropBox img {max-width:100%; max-height:400px; display:block; margin:0 auto;}
    .crop-actions {margin-top:10px; text-align:right;}
    .btn {padding:8px 14px; border-radius:8px; border:1px solid #ccc; background:#f5f5f5; cursor:pointer;}
    .btn.primary {background:#0c48fe; color:#fff; border-color:#0c48fe;}
</style>
@endpush

@section('content')
<main class="content">
    <div class="card profile">
        <div class="profile-container">
            <img id="profilePic" src="{{ asset('img/person.png') }}" alt="Avatar">
            <label for="uploadPic" class="upload-btn">📷</label>
            <input type="file" id="uploadPic" accept="image/*">
        </div>
        <h3 id="nameDisplay">{{ Auth::user()->name ?? 'Admin' }}</h3>
        <p class="role">Peranan: {{ strtoupper(Auth::user()->role ?? 'ADMIN') }}</p>
        <p id="emailDisplay">{{ Auth::user()->email ?? 'admin@example.com' }}</p>
        <button id="editBtn">Edit Profil</button>
    </div>

    <div class="card project">
        <div class="project-header">
            <img src="{{ asset('IMG/PROTON.png') }}" alt="Proton">
            <img src="{{ asset('IMG/ADTCMLK.png') }}" alt="ADTEC">
        </div>
        <h3>Maklumat Akaun</h3>
        <div id="accountForm">
            <label for="accountName">Nama</label>
            <input id="accountName" type="text" value="{{ Auth::user()->name ?? 'Admin' }}">
            <label for="accountEmail">Email</label>
            <input id="accountEmail" type="email" value="{{ Auth::user()->email ?? 'admin@example.com' }}">
        </div>
    </div>
</main>

<!-- Modal Crop -->
<div id="cropModal">
    <div id="cropBox">
        <h3>Crop Gambar Profil</h3>
        <img id="imageToCrop" src="">
        <div class="crop-actions">
            <button id="cancelCrop" class="btn">Batal</button>
            <button id="saveCrop" class="btn primary">Simpan</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let cropper;
    const uploadPic = document.getElementById('uploadPic');
    const profilePic = document.getElementById('profilePic');
    const cropModal = document.getElementById('cropModal');
    const imageToCrop = document.getElementById('imageToCrop');
    const cancelCrop = document.getElementById('cancelCrop');
    const saveCrop = document.getElementById('saveCrop');

    uploadPic.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();

        reader.onload = (event) => {
            imageToCrop.src = event.target.result;
            cropModal.style.display = 'flex';
            imageToCrop.onload = () => {
                if (cropper) cropper.destroy();
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 1,
                    viewMode: 1,
                    background: false,
                });
            };
        };
        reader.readAsDataURL(file);
    });

    cancelCrop.addEventListener('click', () => {
        cropModal.style.display = 'none';
        if (cropper) cropper.destroy();
    });

    saveCrop.addEventListener('click', () => {
        const canvas = cropper.getCroppedCanvas({
            width: 300,
            height: 300,
        });
        profilePic.src = canvas.toDataURL('image/png');
        cropModal.style.display = 'none';
        cropper.destroy();
    });
</script>
@endpush
