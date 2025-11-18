@extends('layouts.staff')

@section('title', 'Notifikasi Staf')

@section('search-placeholder', 'Cari notifikasi...')

@push('styles')
<script src="https://cdn.tailwindcss.com"></script>
@endpush

@section('content')
<div class="bg-white shadow-lg rounded-xl p-6 w-full">
  <h2 class="text-xl font-bold mb-4">Status Permohonan Saya</h2>

  <table class="w-full border-collapse">
    <thead>
      <tr class="border-b">
        <th class="py-2 text-left">Tarikh Permohonan</th>
        <th class="py-2 text-left">Kenderaan</th>
        <th class="py-2 text-left">Destinasi</th>
        <th class="py-2 text-left">Status</th>
        <th class="py-2 text-center">Tindakan</th>
      </tr>
    </thead>
    <tbody>
      @if(isset($bookings) && $bookings->count() > 0)
        @foreach($bookings as $booking)
          <tr class="border-b">
            <td class="py-2">{{ $booking->created_at->format('d/m/Y') }}</td>
            <td class="py-2">{{ $booking->vehicle_name }}</td>
            <td class="py-2">{{ $booking->destination }}</td>
            <td class="py-2">
              <span class="font-semibold 
                {{ $booking->status == 'approved' ? 'text-green-600' : '' }}
                {{ $booking->status == 'rejected' ? 'text-red-600' : '' }}
                {{ $booking->status == 'pending' ? 'text-yellow-600' : '' }}
                {{ $booking->status == 'completed' ? 'text-blue-600' : '' }}
                {{ $booking->status == 'cancelled' ? 'text-gray-600' : '' }}
              ">
                {{ ucfirst($booking->status) }}
              </span>
            </td>
            <td class="text-center py-2">
              <button onclick="openModal(
                '{{ $booking->created_at->format('d/m/Y') }}',
                '{{ $booking->vehicle_name }}',
                '{{ $booking->vehicle_plate ?? 'N/A' }}',
                '{{ $booking->destination }}',
                '{{ $booking->start_date->format('d/m/Y H:i') }}',
                '{{ $booking->end_date->format('d/m/Y H:i') }}',
                '{{ $booking->purpose }}',
                '{{ $booking->notes ?? 'Tiada catatan' }}'
              )" class="text-blue-600 hover:text-blue-800 cursor-pointer">
                <span class="text-xl">👁</span>
              </button>
            </td>
          </tr>
        @endforeach
      @else
        <tr class="border-b">
          <td colspan="5" class="py-4 text-center text-gray-500">Tiada tempahan dijumpai.</td>
        </tr>
      @endif
    </tbody>
  </table>
</div>

<!-- modal view -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white rounded-lg p-6 w-[700px] max-w-[90vw] relative max-h-[90vh] overflow-y-auto">
    <button onclick="closeModal()" class="absolute top-2 right-2 text-xl font-bold text-black hover:text-red-600">✕</button>

    <h3 class="text-lg font-bold mb-4">Maklumat Tempahan</h3>

    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="font-bold text-gray-700">Tarikh Permohonan:</label>
        <input id="modalTarikh" class="w-full border rounded px-3 py-2 bg-gray-50 cursor-default" readonly>
      </div>
      <div>
        <label class="font-bold text-gray-700">No. Plat Kenderaan:</label>
        <input id="modalPlat" class="w-full border rounded px-3 py-2 bg-gray-50 cursor-default" readonly>
      </div>
      <div>
        <label class="font-bold text-gray-700">Nama Kenderaan:</label>
        <input id="modalKenderaan" class="w-full border rounded px-3 py-2 bg-gray-50 cursor-default" readonly>
      </div>
      <div>
        <label class="font-bold text-gray-700">Destinasi:</label>
        <input id="modalDestinasi" class="w-full border rounded px-3 py-2 bg-gray-50 cursor-default" readonly>
      </div>
      <div>
        <label class="font-bold text-gray-700">Tarikh Mula:</label>
        <input id="modalMula" class="w-full border rounded px-3 py-2 bg-gray-50 cursor-default" readonly>
      </div>
      <div>
        <label class="font-bold text-gray-700">Tarikh Tamat:</label>
        <input id="modalTamat" class="w-full border rounded px-3 py-2 bg-gray-50 cursor-default" readonly>
      </div>
    </div>

    <div class="mb-4">
      <label class="font-bold text-gray-700">Tujuan Permohonan:</label>
      <textarea id="modalTujuan" class="w-full border rounded px-3 py-2 bg-gray-50 cursor-default" rows="3" readonly></textarea>
    </div>

    <div class="mb-4">
      <label class="font-bold text-gray-700">Catatan:</label>
      <textarea id="modalCatatan" class="w-full border rounded px-3 py-2 bg-gray-50 cursor-default" rows="2" readonly></textarea>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function openModal(tarikh, kenderaan, plat, destinasi, tarikhMula, tarikhTamat, tujuan, catatan) {
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('modalTarikh').value = tarikh;
    document.getElementById('modalKenderaan').value = kenderaan;
    document.getElementById('modalPlat').value = plat;
    document.getElementById('modalDestinasi').value = destinasi;
    document.getElementById('modalMula').value = tarikhMula;
    document.getElementById('modalTamat').value = tarikhTamat;
    document.getElementById('modalTujuan').value = tujuan;
    document.getElementById('modalCatatan').value = catatan;
  }

  function closeModal(){
    document.getElementById('detailModal').classList.add('hidden');
  }

  // Close modal when clicking outside
  document.getElementById('detailModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
      closeModal();
    }
  });

  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeModal();
    }
  });
</script>
@endpush
