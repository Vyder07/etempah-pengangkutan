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
        <th class="py-2 text-left">Jenis Kenderaan</th>
        <th class="py-2 text-left">Status</th>
        <th class="py-2 text-center">Tindakan</th>
      </tr>
    </thead>
    <tbody>
      @if(isset($applications) && count($applications) > 0)
        @foreach($applications as $app)
          <tr class="border-b">
            <td>{{ $app->application_date }}</td>
            <td>{{ $app->vehicle_type }}</td>
            <td class="font-semibold {{ $app->status == 'DILULUSKAN' ? 'text-green-600' : 'text-red-600' }}">
              {{ $app->status }}
            </td>
            <td class="text-center">
              <button onclick="openModal('{{ $app->application_date }}','{{ $app->vehicle_type }}','{{ $app->memo }}','{{ $app->details }}','{{ $app->department }}')" class="text-blue-600 hover:text-blue-800">👁</button>
            </td>
          </tr>
        @endforeach
      @else
        <tr class="border-b">
          <td>2025-10-23</td>
          <td>Van Kolej</td>
          <td class="text-green-600 font-semibold">DILULUSKAN</td>
          <td class="text-center">
            <button onclick="openModal('2025-10-23','Van Kolej','Memo lawatan pelajar','Permohonan diluluskan oleh pentadbiran untuk lawatan ke Kilang Honda Alor Gajah.','BPPL')" class="text-blue-600 hover:text-blue-800">👁</button>
          </td>
        </tr>
        <tr class="border-b">
          <td>2025-09-29</td>
          <td>Kereta Proton</td>
          <td class="text-red-600 font-semibold">DITOLAK</td>
          <td class="text-center">
            <button onclick="openModal('2025-09-29','Kereta Proton','Memo projek pelajar','Permohonan ditolak kerana jadual bertembung dengan penggunaan kenderaan lain.','HEA')" class="text-blue-600 hover:text-blue-800">👁</button>
          </td>
        </tr>
      @endif
    </tbody>
  </table>
</div>

<!-- modal view -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
  <div class="bg-white rounded-lg p-6 w-[600px] relative">
    <button onclick="closeModal()" class="absolute top-2 right-2 text-xl font-bold text-black">✕</button>

    <h3 class="text-lg font-bold mb-4">Maklumat Permohonan</h3>

    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="font-bold">Bahagian:</label>
        <input id="modalBahagian" class="w-full border rounded px-2 py-1 bg-gray-50 cursor-default" readonly>
      </div>
      <div>
        <label class="font-bold">Tarikh:</label>
        <input id="modalTarikh" class="w-full border rounded px-2 py-1 bg-gray-50 cursor-default" readonly>
      </div>
      <div>
        <label class="font-bold">Kenderaan:</label>
        <input id="modalKenderaan" class="w-full border rounded px-2 py-1 bg-gray-50 cursor-default" readonly>
      </div>
      <div class="col-span-2">
        <label class="font-bold">Memo:</label>
        <div class="flex items-center gap-2 bg-gray-50 border rounded px-2 py-1">
          <span id="modalMemo" class="flex-1 text-gray-700 select-none">-</span>
          <a id="memoDownload" href="#" download="memo.pdf" class="text-blue-600 hover:text-blue-800">⬇</a>
        </div>
      </div>
    </div>

    <div class="mb-4">
      <label class="font-bold">Maklumat:</label>
      <textarea id="modalMaklumat" class="w-full border rounded px-2 py-2 bg-gray-50 cursor-default" rows="4" readonly></textarea>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function openModal(tarikh, kenderaan, memo, maklumat, bahagian) {
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('modalTarikh').value = tarikh;
    document.getElementById('modalKenderaan').value = kenderaan;
    document.getElementById('modalMemo').innerText = memo;
    document.getElementById('modalMaklumat').value = maklumat;
    document.getElementById('modalBahagian').value = bahagian;
    document.getElementById('memoDownload').href = "data:application/pdf;base64,JVBERi0xLjQKJc...";
  }

  function closeModal(){
    document.getElementById('detailModal').classList.add('hidden');
  }
</script>
@endpush
