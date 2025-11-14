@extends('admin.layouts.app')

@section('title', 'Pengurusan Dokumen')

@push('styles')
<style>
    .table-card { background:white; border-radius:12px; box-shadow:0 6px 18px rgba(0,0,0,0.06); padding:18px; max-width:900px; margin:auto; }
    .action-btn { cursor:pointer; font-size:16px; padding:8px 12px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; }
    .staff-name { font-size:0.85rem; color:#6b7280; }
    .trash-bin { position:fixed; bottom:16px; right:16px; background:#f87171; color:white; width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 6px rgba(0,0,0,0.2); }
    #previewModal { display:none; position:fixed; inset:0; z-index:50; align-items:center; justify-content:center; }
    #previewModal.active { display:flex; }
    #previewModal .overlay { position:absolute; inset:0; background: rgba(0,0,0,0.5); }
    #previewModal .modal-content { position:relative; background:white; border-radius:12px; padding:18px; width:90%; max-width:3xl; z-index:10; }
</style>
@endpush

@section('content')
<main class="content">
    <div class="table-card">
        <h1 class="text-2xl font-semibold mb-4">Pengurusan Dokumen (Demo)</h1>

        <table class="w-full text-left">
            <thead>
                <tr class="text-sm text-gray-600 border-b">
                    <th class="py-3">Nama Fail & Info</th>
                    <th class="py-3 text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody id="docTable"></tbody>
        </table>
    </div>
</main>

<!-- Trash Bin -->
<div class="trash-bin" title="Trash Bin" onclick="alert('Buka trash bin demo')">🗑️</div>

<!-- Preview Modal -->
<div id="previewModal">
    <div class="overlay" onclick="closePreview()"></div>
    <div class="modal-content">
        <div class="flex items-start justify-between mb-3">
            <h2 id="previewTitle" class="text-lg font-semibold">Preview</h2>
            <button class="text-gray-600" onclick="closePreview()">✖</button>
        </div>
        <div id="previewBody" class="h-[60vh] bg-gray-100 overflow-auto border rounded"></div>
        <div class="flex justify-end mt-3 gap-2">
            <button class="px-3 py-2 bg-red-500 text-white rounded" onclick="closePreview()">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const docs = [
        { id:1, name:'memo_lawatan_pelajar.pdf', date:'2025-09-10', size:'432 KB', summary:'Memo lawatan pelajar ke kilang A.', staff:'Irfan Aidil' },
        { id:2, name:'surat_kelulusan_tempahan.pdf', date:'2025-09-05', size:'210 KB', summary:'Surat kelulusan tempahan kenderaan.', staff:'Puan Natasya' },
        { id:3, name:'jadual_kenderaan_sep2025.pdf', date:'2025-08-28', size:'128 KB', summary:'Jadual penggunaan kenderaan bagi bulan September 2025.', staff:'Ustaz Sazali' },
        { id:4, name:'borang_permohonan_xyz.pdf', date:'2025-08-10', size:'56 KB', summary:'Borang permohonan untuk lawatan luar.', staff:'Irfan Aidil' },
        { id:5, name:'laporan_penggunaan_kenderaan.pdf', date:'2025-07-21', size:'1.2 MB', summary:'Laporan ringkas penggunaan kenderaan jabatan.', staff:'Puan Natasya' }
    ];

    function escapeHtml(str){ return String(str).replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]); }

    function renderTable(){
        const tbody=document.getElementById('docTable');
        tbody.innerHTML='';
        docs.forEach(doc=>{
            const tr=document.createElement('tr'); tr.className='border-b';
            tr.innerHTML=`
                <td class="py-3">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-3">
                            <svg width="30" height="30" viewBox="0 0 24 24" class="text-gray-500">
                                <path fill="currentColor" d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"></path>
                            </svg>
                            <div class="font-medium text-gray-800">${escapeHtml(doc.name)}</div>
                        </div>
                        <div class="staff-name">Staff: ${escapeHtml(doc.staff)} • Saiz: ${doc.size}</div>
                    </div>
                </td>
                <td class="py-3 text-center flex justify-center gap-2">
                    <button class="action-btn bg-blue-500 p-2 rounded text-white hover:bg-blue-600" onclick="viewDoc(${doc.id})">
                        <span class="material-icons">visibility</span>
                    </button>
                    <button class="action-btn bg-green-600 p-2 rounded text-white hover:bg-green-700" onclick="downloadDoc(${doc.id})">
                        <span class="material-icons">download</span>
                    </button>
                    <button class="action-btn bg-red-600 p-2 rounded text-white hover:bg-red-700" onclick="deleteDoc(${doc.id})">
                        <span class="material-icons">delete</span>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function viewDoc(id){
        const doc=docs.find(d=>d.id===id);
        if(!doc) return alert('Dokumen tidak ditemui.');
        document.getElementById('previewTitle').innerText=doc.name;
        document.getElementById('previewBody').innerHTML=`
            <div style="padding:18px">
                <h3 style="margin:0 0 6px;font-size:18px">${escapeHtml(doc.name)}</h3>
                <p style="margin:0 0 10px;color:#555"><strong>Tarikh:</strong> ${doc.date}</p>
                <hr style="border:none;border-top:1px solid #e5e7eb;margin:12px 0">
                <div style="font-size:14px;color:#333;line-height:1.6">
                    <p><strong>Ringkasan:</strong> ${escapeHtml(doc.summary)}</p>
                    <p>This is a <em>decoy preview</em> for demo only.</p>
                </div>
            </div>
        `;
        document.getElementById('previewModal').classList.add('active');
    }

    function closePreview(){ document.getElementById('previewModal').classList.remove('active'); document.getElementById('previewBody').innerHTML=''; }

    function downloadDoc(id){
        const doc=docs.find(d=>d.id===id);
        if(!doc) return alert('Dokumen tidak ditemui.');
        const blob=new Blob([`Decoy PDF: ${doc.name}\nStaff: ${doc.staff}\nDate: ${doc.date}\nSummary: ${doc.summary}`], {type:'application/pdf'});
        const url=URL.createObjectURL(blob);
        const a=document.createElement('a'); a.href=url; a.download=doc.name; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
    }

    function deleteDoc(id){
        if(!confirm('Padam dokumen ini? Tindakan ini tidak boleh dikembalikan dalam demo.')) return;
        const idx=docs.findIndex(d=>d.id===id);
        if(idx!==-1) docs.splice(idx,1);
        renderTable();
    }

    renderTable();
</script>
@endpush
