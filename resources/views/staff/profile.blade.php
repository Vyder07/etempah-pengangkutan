@extends('layouts.staff')

@section('title', 'Profil Staf')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
  .content{display:flex;justify-content:center;align-items:flex-start;gap:30px;padding:20px;flex-wrap:wrap;}
  .card{background:rgba(255,255,255,0.95);border-radius:15px;box-shadow:0px 4px 10px rgba(0,0,0,0.2);padding:20px;}
  .card.profile{width:340px;text-align:center;}
  .profile-container{position:relative;display:inline-block;margin-bottom:10px;}
  .profile-container img{width:120px;height:120px;border-radius:50%;object-fit:cover;background:#eee;cursor:default;border:3px solid #0c48fe;}
  .upload-btn{position:absolute;bottom:0;right:0;background:rgb(9,30,255);color:white;border-radius:50%;padding:6px;font-size:14px;cursor:pointer;}
  input[type="file"]{display:none;}
  .role{color:red;font-weight:bold;margin-bottom:15px;}
  label{font-weight:bold;display:block;margin-top:10px;text-align:left;}
  input{width:100%;padding:8px;border-radius:8px;border:1px solid #ccc;background:#eee;}
  .card.profile button{margin-top:15px;width:100%;padding:10px;background:#0c48fe;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:bold;}
  .card.profile button:hover{background:#0734bb;}
  .card.project{width:500px;text-align:left;}
  .project-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;}
  .project-header img{max-height:50px;}
  /* Modal crop */
  #cropModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);justify-content:center;align-items:center;z-index:9999;}
  #cropBox{background:white;padding:20px;border-radius:10px;max-width:500px;text-align:center;}
  #cropBox img{max-width:100%;max-height:400px;display:block;margin:0 auto;}
  .crop-actions{margin-top:10px;text-align:right;}
  .btn{padding:8px 14px;border-radius:8px;border:1px solid #ccc;background:#f5f5f5;cursor:pointer;}
  .btn.primary{background:#0c48fe;color:#fff;border-color:#0c48fe;}
</style>
@endpush

@section('content')
<div class="card profile">
  <div class="profile-container">
    <img id="profilePic" src="{{ auth()->user()->profile_photo ?? asset('img/person.png') }}" alt="Avatar">
    <label for="uploadPic" class="upload-btn">📷</label>
    <input type="file" id="uploadPic" accept="image/*">
  </div>
  <h3 id="nameDisplay">{{ auth()->user()->name ?? 'Pn. Aina' }}</h3>
  <p class="role">Peranan: {{ strtoupper(auth()->user()->role ?? 'STAFF') }}</p>
  <p id="emailDisplay">{{ auth()->user()->email ?? 'aina@example.com' }}</p>
  <button id="editBtn">Edit Profile</button>
</div>

<div class="card project">
  <div class="project-header">
    <img src="{{ asset('IMG/PROTON.png') }}" alt="Proton">
    <img src="{{ asset('IMG/ADTCMLK.png') }}" alt="ADTEC">
  </div>
  <h3>Maklumat Akaun</h3>
  <form id="accountForm" action="{{ route('staff.profile.update') }}" method="POST">
    @csrf
    @method('PUT')
    <label for="accountName">Nama</label>
    <input id="accountName" name="name" type="text" value="{{ auth()->user()->name ?? 'Pn. Aina' }}">
    <label for="accountEmail">Email</label>
    <input id="accountEmail" name="email" type="email" value="{{ auth()->user()->email ?? 'aina@example.com' }}">
    <button type="submit" style="margin-top: 15px; width: 100%; padding: 10px; background: #0c48fe; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">Simpan Perubahan</button>
  </form>
</div>

<!-- Modal Crop -->
<div id="cropModal">
  <div id="cropBox">
    <h3>Crop Gambar Profil</h3>
    <img id="imageToCrop">
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
  const cancelBtn = document.getElementById('cancelCrop');
  const saveBtn = document.getElementById('saveCrop');

  // bila user pilih gambar
  uploadPic.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (event) {
      imageToCrop.src = event.target.result;
      cropModal.style.display = 'flex';
      if (cropper) cropper.destroy();
      cropper = new Cropper(imageToCrop, {
        aspectRatio: 1,
        viewMode: 1,
        background: false,
      });
    };
    reader.readAsDataURL(file);
  });

  // bila klik BATAL
  cancelBtn.addEventListener('click', () => {
    cropModal.style.display = 'none';
    if (cropper) cropper.destroy();
  });

  // bila klik SIMPAN
  saveBtn.addEventListener('click', () => {
    const canvas = cropper.getCroppedCanvas({
      width: 200,
      height: 200,
    });

    // Convert to blob and upload
    canvas.toBlob((blob) => {
      const formData = new FormData();
      formData.append('_token', '{{ csrf_token() }}');
      formData.append('profile_photo', blob, 'profile.png');

      fetch('{{ route('staff.profile.photo') }}', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          profilePic.src = canvas.toDataURL('image/png');
          cropModal.style.display = 'none';
          cropper.destroy();
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Gagal memuat naik gambar');
      });
    });
  });
</script>
@endpush
