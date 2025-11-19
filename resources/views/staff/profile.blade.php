@extends('layouts.staff')

@section('title', 'Profil Staf')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css"/>
<style>
  /* Modal crop */
  #cropModal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);justify-content:center;align-items:center;z-index:9999;}
  #cropBox{background:white;padding:20px;border-radius:10px;max-width:500px;text-align:center;}
  #cropBox img{max-width:100%;max-height:400px;display:block;margin:0 auto;}

  /* Toast Notification */
  .toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #10b981;
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 10000;
    animation: slideIn 0.3s ease-out;
    min-width: 300px;
  }

  .toast.error {
    background: #ef4444;
  }

  .toast.hide {
    animation: slideOut 0.3s ease-out forwards;
  }

  .toast-icon {
    font-size: 24px;
  }

  .toast-content {
    flex: 1;
  }

  .toast-title {
    font-weight: 600;
    margin-bottom: 4px;
  }

  .toast-message {
    font-size: 14px;
    opacity: 0.9;
  }

  .toast-close {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    line-height: 1;
  }

  @keyframes slideIn {
    from {
      transform: translateX(400px);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }

  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(400px);
      opacity: 0;
    }
  }
</style>
@endpush

@section('content')
<div class="flex justify-center items-start gap-8 flex-wrap">
  <!-- Profile Card -->
  <div class="bg-white/95 rounded-2xl shadow-xl p-8 w-full max-w-sm text-center">
    <div class="relative inline-block mb-4">
      <img id="profilePic" src="{{ auth()->user()->profile_photo_url }}" alt="Avatar"
           class="w-32 h-32 rounded-full object-cover border-4 border-blue-600">
      <label for="uploadPic" class="absolute bottom-0 right-0 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 cursor-pointer transition">
        <span class="material-symbols-outlined text-xl">photo_camera</span>
      </label>
      <input type="file" id="uploadPic" accept="image/*" class="hidden">
    </div>

    <h3 id="nameDisplay" class="text-2xl font-semibold text-gray-800 mb-2">{{ auth()->user()->name }}</h3>
    <p class="text-red-600 font-bold mb-3">Peranan: {{ strtoupper(auth()->user()->role ?? 'STAFF') }}</p>
    <p id="emailDisplay" class="text-gray-600 mb-6">{{ auth()->user()->email }}</p>

    <button id="editBtn" onclick="document.getElementById('accountName').focus()"
            class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition shadow-lg">
      Edit Profile
    </button>
  </div>

  <!-- Account Info Card -->
  <div class="bg-white/95 rounded-2xl shadow-xl p-8 w-full max-w-lg">
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-200">
      <img src="{{ asset('IMG/PROTON.png') }}" alt="Proton" class="h-12">
      <img src="{{ asset('IMG/ADTCMLK.png') }}" alt="ADTEC" class="h-12">
    </div>

    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Maklumat Akaun</h3>

    <form id="accountForm" action="{{ route('staff.profile.update') }}" method="POST" class="space-y-5">
      @csrf
      @method('PUT')

      <div>
        <label for="accountName" class="block text-sm font-semibold text-gray-700 mb-2">Nama</label>
        <input id="accountName" name="name" type="text" value="{{ auth()->user()->name }}" required
               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
      </div>

      <div>
        <label for="accountEmail" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
        <input id="accountEmail" name="email" type="email" value="{{ auth()->user()->email }}" required
               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-gray-50">
      </div>

      <button type="submit"
              class="w-full py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition shadow-lg mt-6">
        Simpan Perubahan
      </button>
    </form>
  </div>
</div>

<!-- Modal Crop -->
<div id="cropModal" class="hidden fixed inset-0 bg-black/60 justify-center items-center z-[9999]">
  <div id="cropBox" class="bg-white rounded-2xl p-6 max-w-lg w-full mx-4 shadow-2xl">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Crop Gambar Profil</h3>
    <img id="imageToCrop" class="max-w-full max-h-96 mx-auto block rounded-lg">
    <div class="flex justify-end gap-3 mt-6">
      <button id="cancelCrop" class="px-5 py-2 rounded-lg border border-gray-300 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium transition">
        Batal
      </button>
      <button id="saveCrop" class="px-5 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-medium transition shadow-lg">
        Simpan
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
  // Toast Notification Function
  function showToast(title, message, type = 'success') {
    // Remove existing toast if any
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
      existingToast.remove();
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;

    const icon = type === 'success' ? '✓' : '✕';

    toast.innerHTML = `
      <span class="toast-icon">${icon}</span>
      <div class="toast-content">
        <div class="toast-title">${title}</div>
        <div class="toast-message">${message}</div>
      </div>
      <button class="toast-close" onclick="this.parentElement.remove()">×</button>
    `;

    document.body.appendChild(toast);

    // Auto remove after 5 seconds
    setTimeout(() => {
      toast.classList.add('hide');
      setTimeout(() => toast.remove(), 300);
    }, 5000);
  }

  // Handle Profile Form Submission
  document.getElementById('accountForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.textContent = 'Menyimpan...';

    // Create FormData
    const formData = new FormData(form);

    // Send AJAX request
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update display values
        document.getElementById('nameDisplay').textContent = formData.get('name');
        document.getElementById('emailDisplay').textContent = formData.get('email');

        // Show success toast
        showToast('Berjaya!', 'Maklumat profil telah dikemas kini', 'success');
      } else {
        showToast('Ralat!', data.message || 'Gagal menyimpan perubahan', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('Ralat!', 'Ralat berlaku semasa menyimpan. Sila cuba lagi.', 'error');
    })
    .finally(() => {
      // Re-enable button
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    });
  });

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
      cropModal.classList.remove('hidden');
      cropModal.classList.add('flex');
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
    cropModal.classList.add('hidden');
    cropModal.classList.remove('flex');
    if (cropper) cropper.destroy();
    uploadPic.value = '';
  });

  // bila klik SIMPAN
  saveBtn.addEventListener('click', () => {
    const canvas = cropper.getCroppedCanvas({
      width: 300,
      height: 300,
    });

    // Show loading state
    saveBtn.disabled = true;
    saveBtn.textContent = 'Memuat naik...';

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
          // Update profile pic with the actual URL from server
          profilePic.src = data.photo_url + '?t=' + new Date().getTime();
          cropModal.classList.add('hidden');
          cropModal.classList.remove('flex');
          if (cropper) cropper.destroy();

          // Show success toast
          showToast('Berjaya!', data.message || 'Gambar profil berjaya dikemas kini!', 'success');

          // Reset file input
          uploadPic.value = '';
        } else {
          showToast('Ralat!', data.message || 'Gagal memuat naik gambar', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Ralat!', 'Gagal memuat naik gambar. Sila cuba lagi.', 'error');
      })
      .finally(() => {
        // Reset button state
        saveBtn.disabled = false;
        saveBtn.textContent = 'Simpan';
      });
    }, 'image/png');
  });
</script>
@endpush
