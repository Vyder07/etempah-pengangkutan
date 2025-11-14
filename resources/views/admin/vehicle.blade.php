@extends('admin.layouts.app')

@section('title', 'Pengurusan Dokumen')

@section('content')
<main class="ml-[260px] w-full p-6">
    <div class="bg-white rounded-xl shadow-md p-5 mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Pengurusan Dokumen Tempahan</h1>
            <span id="docCount" class="text-sm text-gray-500"></span>
        </div>

        <table class="w-full text-left">
            <thead>
                <tr class="text-sm text-gray-600 border-b">
                    <th class="py-3">Nama Fail & Info</th>
                    <th class="py-3 text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody id="docTable">
                <tr>
                    <td colspan="2" class="py-8 text-center text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-icons text-5xl opacity-50">hourglass_empty</span>
                            <p>Memuatkan dokumen...</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<!-- Preview Modal -->
<div id="previewModal" class="hidden fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-black/50" onclick="closePreview()"></div>
    <div class="relative bg-white rounded-xl p-5 w-[90%] max-w-4xl z-10 max-h-[90vh] overflow-auto">
        <div class="flex items-start justify-between mb-3">
            <h2 id="previewTitle" class="text-lg font-semibold">Preview</h2>
            <button class="text-gray-600 hover:text-gray-900" onclick="closePreview()">✖</button>
        </div>
        <div id="previewBody" class="min-h-[60vh] bg-gray-100 overflow-auto border rounded"></div>
        <div class="flex justify-end mt-3 gap-2">
            <button class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors" onclick="closePreview()">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    let docs = [];

    function escapeHtml(str){
        return String(str).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]);
    }

    // Fetch documents from server
    async function fetchDocuments() {
        try {
            const response = await fetch('{{ route("admin.documents.data") }}');
            const data = await response.json();
            docs = data;
            renderTable();
        } catch (error) {
            console.error('Error fetching documents:', error);
            document.getElementById('docTable').innerHTML = `
                <tr>
                    <td colspan="2" class="py-8 text-center text-red-500">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-icons text-5xl opacity-50">error_outline</span>
                            <p>Gagal memuatkan dokumen</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    function renderTable(){
        const tbody = document.getElementById('docTable');
        const docCount = document.getElementById('docCount');

        if (docs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="2" class="py-8 text-center text-gray-400">
                        <div class="flex flex-col items-center gap-2">
                            <span class="material-icons text-5xl opacity-50">folder_open</span>
                            <p>Tiada dokumen dijumpai</p>
                        </div>
                    </td>
                </tr>
            `;
            docCount.textContent = '';
            return;
        }

        docCount.textContent = `${docs.length} dokumen`;
        tbody.innerHTML = '';

        docs.forEach(doc => {
            const tr = document.createElement('tr');
            tr.className = 'border-b hover:bg-gray-50 transition-colors';

            const fileIcon = getFileIcon(doc.mime_type);

            tr.innerHTML = `
                <td class="py-3">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-3">
                            ${fileIcon}
                            <div>
                                <div class="font-medium text-gray-800">${escapeHtml(doc.name)}</div>
                                <div class="text-xs text-gray-500 mt-1">${escapeHtml(doc.summary)}</div>
                            </div>
                        </div>
                        <div class="text-[0.85rem] text-gray-500 ml-11">
                            Staff: ${escapeHtml(doc.staff)} • Saiz: ${doc.size} • ${doc.date}
                        </div>
                    </div>
                </td>
                <td class="py-3">
                    <div class="flex justify-center gap-2">
                        <button class="cursor-pointer text-base p-2 rounded inline-flex items-center justify-center bg-blue-500 text-white hover:bg-blue-600 transition-colors" onclick="viewDoc(${doc.id})" title="Lihat">
                            <span class="material-icons">visibility</span>
                        </button>
                        <button class="cursor-pointer text-base p-2 rounded inline-flex items-center justify-center bg-green-600 text-white hover:bg-green-700 transition-colors" onclick="downloadDoc(${doc.id})" title="Muat Turun">
                            <span class="material-icons">download</span>
                        </button>
                        <button class="cursor-pointer text-base p-2 rounded inline-flex items-center justify-center bg-red-600 text-white hover:bg-red-700 transition-colors" onclick="deleteDoc(${doc.id})" title="Padam">
                            <span class="material-icons">delete</span>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function getFileIcon(mimeType) {
        if (mimeType.includes('pdf')) {
            return '<svg width="30" height="30" viewBox="0 0 24 24" class="text-red-500"><path fill="currentColor" d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path></svg>';
        } else if (mimeType.includes('image')) {
            return '<svg width="30" height="30" viewBox="0 0 24 24" class="text-blue-500"><path fill="currentColor" d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"></path></svg>';
        } else if (mimeType.includes('word') || mimeType.includes('document')) {
            return '<svg width="30" height="30" viewBox="0 0 24 24" class="text-blue-600"><path fill="currentColor" d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path></svg>';
        } else {
            return '<svg width="30" height="30" viewBox="0 0 24 24" class="text-gray-500"><path fill="currentColor" d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path></svg>';
        }
    }

    function viewDoc(id){
        const doc = docs.find(d => d.id === id);
        if (!doc) return alert('Dokumen tidak ditemui.');

        document.getElementById('previewTitle').innerText = doc.name;

        let previewContent = '';

        if (doc.mime_type.includes('image')) {
            previewContent = `<img src="${doc.url}" alt="${escapeHtml(doc.name)}" class="w-full h-auto">`;
        } else if (doc.mime_type.includes('pdf')) {
            previewContent = `<iframe src="${doc.url}" class="w-full h-[70vh]" frameborder="0"></iframe>`;
        } else {
            previewContent = `
                <div class="p-6 text-center">
                    <span class="material-icons text-6xl text-gray-400 mb-3">description</span>
                    <h3 class="text-lg font-semibold mb-2">${escapeHtml(doc.name)}</h3>
                    <p class="text-gray-600 mb-4">Previu tidak tersedia untuk jenis fail ini</p>
                    <a href="${doc.url}" download="${escapeHtml(doc.name)}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <span class="material-icons">download</span>
                        Muat Turun Fail
                    </a>
                </div>
            `;
        }

        document.getElementById('previewBody').innerHTML = previewContent;
        document.getElementById('previewModal').classList.remove('hidden');
        document.getElementById('previewModal').classList.add('flex');
    }

    function closePreview(){
        document.getElementById('previewModal').classList.add('hidden');
        document.getElementById('previewModal').classList.remove('flex');
        document.getElementById('previewBody').innerHTML = '';
    }

    function downloadDoc(id){
        const doc = docs.find(d => d.id === id);
        if (!doc) return alert('Dokumen tidak ditemui.');

        window.open(doc.url, '_blank');
    }

    async function deleteDoc(id){
        if (!confirm('Padam dokumen ini? Tindakan ini tidak boleh dikembalikan.')) return;

        try {
            const response = await fetch(`{{ url('admin/documents') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                // Remove from local array
                const idx = docs.findIndex(d => d.id === id);
                if (idx !== -1) docs.splice(idx, 1);
                renderTable();
            } else {
                alert('Ralat: ' + (result.message || 'Gagal memadam dokumen'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ralat semasa memadam dokumen');
        }
    }

    // Load documents on page load
    fetchDocuments();
</script>
@endpush
