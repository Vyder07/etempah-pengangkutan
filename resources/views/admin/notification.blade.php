@extends('admin.layouts.app')

@section('title', 'Notifikasi Admin')

@section('content')
<main class="content ml-[260px] p-6">
    <div class="bg-white shadow-lg rounded-xl p-6 w-full">
        <h2 class="text-xl font-bold mb-4">Senarai Notifikasi Permohonan</h2>

        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2 text-left">Nama Pemohon</th>
                    <th class="py-2 text-left">Status</th>
                    <th class="py-2 text-center">Tindakan</th>
                    <th class="py-2 text-left">Tarikh</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="py-3 flex items-center gap-2">
                        <img src="https://via.placeholder.com/30" class="rounded-full">
                        Irfan Aidil
                    </td>
                    <td class="text-yellow-600 font-semibold">MENUNGGU</td>
                    <td class="text-center">
                        <button onclick="openModal('Irfan Aidil','BPPL','2025-10-23','Memo lawatan pelajar','Memohon untuk meminjam van kolej pada 23/10/2025 bagi tujuan menghantar pelajar ke lawatan sambil belajar ke Kilang Honda Alor Gajah.','Van Kolej',this)" class="text-blue-600 hover:text-blue-800">
                            👁
                        </button>
                    </td>
                    <td>2025-10-23</td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 flex items-center gap-2">
                        <img src="https://via.placeholder.com/30" class="rounded-full">
                        Puan Natasya
                    </td>
                    <td class="text-red-600 font-semibold">DITOLAK</td>
                    <td class="text-center">
                        <button onclick="openModal('Puan Natasya','HEA','2025-09-29','Memo projek pelajar','Permohonan menggunakan kenderaan ditolak kerana jadual bertembung.','Kereta Proton',this)" class="text-blue-600 hover:text-blue-800">
                            👁
                        </button>
                    </td>
                    <td>2025-09-29</td>
                </tr>
                <tr class="border-b">
                    <td class="py-3 flex items-center gap-2">
                        <img src="https://via.placeholder.com/30" class="rounded-full">
                        Ustaz Sazali
                    </td>
                    <td class="text-red-600 font-semibold">DITOLAK</td>
                    <td class="text-center">
                        <button onclick="openModal('Ustaz Sazali','TVET TAHFIZ','2025-09-29','Memo pertandingan hafazan Negeri Melaka','Permohonan menggunakan Van Institut untuk menghantar pelajar tahfiz terpilih untuk menyertai pertandingan hafazan.','VAN Adtec',this)" class="text-blue-600 hover:text-blue-800">
                            👁
                        </button>
                    </td>
                    <td>2025-09-29</td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<!-- popup detail -->
<div id="detailModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg p-6 w-[600px] relative">
        <button onclick="closeModal()" class="absolute top-2 right-2 text-xl font-bold text-black">✕</button>

        <h3 class="text-lg font-bold mb-4">Detail Permohonan</h3>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="font-bold">Nama:</label>
                <input id="modalNama" class="w-full border rounded px-2 py-1 bg-gray-50 cursor-default" readonly>
            </div>
            <div>
                <label class="font-bold">Tarikh Permohonan:</label>
                <input id="modalTarikh" class="w-full border rounded px-2 py-1 bg-gray-50 cursor-default" readonly>
            </div>
            <div>
                <label class="font-bold">Bahagian:</label>
                <input id="modalBahagian" class="w-full border rounded px-2 py-1 bg-gray-50 cursor-default" readonly>
            </div>
            <div>
                <label class="font-bold">Jenis Kenderaan:</label>
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
            <label class="font-bold">Maklumat Permohonan:</label>
            <textarea id="modalMaklumat" class="w-full border rounded px-2 py-2 bg-gray-50 cursor-default" rows="4" readonly></textarea>
        </div>

        <div class="flex gap-4 justify-center">
            <button id="approveBtn" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">LULUSKAN</button>
            <button id="rejectBtn" class="bg-gray-200 text-black px-4 py-2 rounded hover:bg-gray-300">TOLAK</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentRow = null;

    function openModal(nama, bahagian, tarikh, memo, maklumat, kenderaan, btn) {
        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('modalNama').value = nama;
        document.getElementById('modalBahagian').value = bahagian;
        document.getElementById('modalTarikh').value = tarikh;
        document.getElementById('modalMemo').innerText = memo;
        document.getElementById('modalMaklumat').value = maklumat;
        document.getElementById('modalKenderaan').value = kenderaan;
        document.getElementById('memoDownload').href = "data:application/pdf;base64,JVBERi0xLjQKJc..."; // dummy pdf
        currentRow = btn.closest("tr");
    }

    function closeModal() {
        if(currentRow){
            let statusCell = currentRow.querySelector("td:nth-child(2)");
            if(statusCell.innerText !== "DILULUSKAN" && statusCell.innerText !== "DITOLAK"){
                statusCell.innerText = "MENUNGGU";
                statusCell.className = "text-yellow-600 font-semibold";
            }
        }
        document.getElementById('detailModal').classList.add('hidden');
    }

    document.getElementById("approveBtn").addEventListener("click", function(){
        if(currentRow){
            let statusCell = currentRow.querySelector("td:nth-child(2)");
            statusCell.innerText = "DILULUSKAN";
            statusCell.className = "text-green-600 font-semibold";
            dimEye(currentRow);
            closeModal();
        }
    });

    document.getElementById("rejectBtn").addEventListener("click", function(){
        if(currentRow){
            let statusCell = currentRow.querySelector("td:nth-child(2)");
            statusCell.innerText = "DITOLAK";
            statusCell.className = "text-red-600 font-semibold";
            dimEye(currentRow);
            closeModal();
        }
    });

    function dimEye(row){
        let eyeBtn = row.querySelector("button");
        if(eyeBtn) eyeBtn.classList.add("opacity-50","cursor-not-allowed");
    }
</script>
@endpush
