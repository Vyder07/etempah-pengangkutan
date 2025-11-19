@extends('admin.layouts.app')

@section('title', 'Kemaskini Kalendar')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    .calendar-container {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .calendar-header h1 {
        font-size: 1.8em;
        font-weight: 600;
        color: #333;
        margin: 0;
    }

    .calendar-controls {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .view-toggle {
        display: flex;
        gap: 5px;
        background: #f3f4f6;
        padding: 4px;
        border-radius: 8px;
    }

    .view-toggle button {
        padding: 8px 16px;
        border: none;
        background: transparent;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        color: #6b7280;
        transition: all 0.3s;
    }

    .view-toggle button.active {
        background: white;
        color: #2563eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .view-toggle button:hover:not(.active) {
        color: #374151;
    }

    .legend {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 15px;
        padding: 15px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* FullCalendar customizations */
    .fc {
        font-family: inherit;
    }

    .fc-event {
        cursor: pointer;
        border: none;
        padding: 2px 4px;
    }

    .fc-daygrid-event {
        margin: 1px 2px;
    }

    .fc-toolbar-title {
        font-size: 1.5em !important;
        font-weight: 600 !important;
    }

    .fc-button {
        text-transform: capitalize !important;
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
        padding: 20px;
    }

    .modal.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 12px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }

    .modal-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 1.25em;
        font-weight: 600;
        color: #333;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .modal-close:hover {
        background: #f3f4f6;
        color: #333;
    }

    .modal-body {
        padding: 25px;
    }

    .detail-row {
        margin-bottom: 20px;
    }

    .detail-label {
        font-weight: 600;
        color: #555;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .detail-value {
        color: #333;
        font-size: 15px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        color: white;
    }

    .status-pending { background: #f59e0b; }
    .status-approved { background: #10b981; }
    .status-rejected { background: #ef4444; }
    .status-completed { background: #3b82f6; }
    .status-cancelled { background: #6b7280; }

    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
        flex: 1;
    }

    .btn-approve {
        background: #10b981;
        color: white;
    }

    .btn-approve:hover {
        background: #059669;
    }

    .btn-reject {
        background: #ef4444;
        color: white;
    }

    .btn-reject:hover {
        background: #dc2626;
    }

    .btn-complete {
        background: #3b82f6;
        color: white;
    }

    .btn-complete:hover {
        background: #2563eb;
    }

    .notes-input {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        resize: vertical;
        min-height: 80px;
        margin-top: 10px;
    }
</style>
@endpush

@section('content')
<div class="kotak">
    <div class="calendar-header">
        <h1>Kalendar Tempahan Kenderaan</h1>
        <div class="calendar-controls">
            <div class="view-toggle">
                <button id="monthView" class="active" data-view="month">Bulan</button>
                <button id="weekView" data-view="week">Minggu</button>
                <button id="dayView" data-view="day">Hari</button>
            </div>
        </div>
    </div>

    <div class="calendar-container">
        <div id="calendar"></div>
    </div>

    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background: #f59e0b;"></div>
            <span>Menunggu Kelulusan</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #10b981;"></div>
            <span>Diluluskan</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #ef4444;"></div>
            <span>Ditolak</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #3b82f6;"></div>
            <span>Selesai</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background: #6b7280;"></div>
            <span>Dibatal</span>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div class="modal" id="bookingModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Butiran Tempahan</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="detail-row">
                <div class="detail-label">Kenderaan</div>
                <div class="detail-value" id="modalVehicle"></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">No. Pendaftaran</div>
                <div class="detail-value" id="modalPlate"></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Ditempah Oleh</div>
                <div class="detail-value" id="modalUser"></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Destinasi</div>
                <div class="detail-value" id="modalDestination"></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Tujuan</div>
                <div class="detail-value" id="modalPurpose"></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Tarikh Mula</div>
                <div class="detail-value" id="modalStart"></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Tarikh Tamat</div>
                <div class="detail-value" id="modalEnd"></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Status</div>
                <div class="detail-value">
                    <span class="status-badge" id="modalStatus"></span>
                </div>
            </div>

            <div class="detail-row" id="notesRow" style="display: none;">
                <div class="detail-label">Nota</div>
                <div class="detail-value" id="modalNotes"></div>
            </div>

            <div class="modal-actions" id="modalActions">
                <button class="btn btn-approve" onclick="updateStatus('approved')">
                    Luluskan
                </button>
                <button class="btn btn-reject" onclick="updateStatus('rejected')">
                    Tolak
                </button>
                <button class="btn btn-complete" onclick="updateStatus('completed')">
                    Tandakan Selesai
                </button>
                <button class="btn btn-download" onclick="downloadPDF()" style="background: #10b981;">
                    <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">download</span>
                    Muat Turun PDF
                </button>
            </div>

            <div id="notesSection" style="display: none; margin-top: 15px;">
                <label class="detail-label">Nota Tambahan</label>
                <textarea id="statusNotes" class="notes-input" placeholder="Masukkan nota (opsional)"></textarea>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    let calendar;
    let currentBookingId = null;
    const csrfToken = '{{ csrf_token() }}';

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            buttonText: {
                today: 'Hari Ini'
            },
            events: '{{ route("admin.bookings.data") }}',
            eventClick: function(info) {
                showBookingDetails(info.event);
            },
            eventDisplay: 'block',
            displayEventTime: true,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            height: 'auto'
        });

        calendar.render();

        // View toggle buttons
        document.querySelectorAll('.view-toggle button').forEach(button => {
            button.addEventListener('click', function() {
                const view = this.dataset.view;

                // Update active state
                document.querySelectorAll('.view-toggle button').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');

                // Change calendar view
                switch(view) {
                    case 'month':
                        calendar.changeView('dayGridMonth');
                        break;
                    case 'week':
                        calendar.changeView('timeGridWeek');
                        break;
                    case 'day':
                        calendar.changeView('timeGridDay');
                        break;
                }
            });
        });
    });

    function showBookingDetails(event) {
        const props = event.extendedProps;
        currentBookingId = event.id;

        // Populate modal
        document.getElementById('modalVehicle').textContent = event.title;
        document.getElementById('modalPlate').textContent = props.vehicle_plate || '-';
        document.getElementById('modalUser').textContent = props.user;
        document.getElementById('modalDestination').textContent = props.destination;
        document.getElementById('modalPurpose').textContent = props.purpose;
        document.getElementById('modalStart').textContent = formatDateTime(event.start);
        document.getElementById('modalEnd').textContent = formatDateTime(event.end);

        const statusBadge = document.getElementById('modalStatus');
        statusBadge.textContent = props.status_label;
        statusBadge.className = 'status-badge status-' + props.status;

        // Show/hide notes
        if (props.notes) {
            document.getElementById('modalNotes').textContent = props.notes;
            document.getElementById('notesRow').style.display = 'block';
        } else {
            document.getElementById('notesRow').style.display = 'none';
        }

        // Show/hide action buttons based on status
        const actionsDiv = document.getElementById('modalActions');
        const downloadBtn = '<button class="btn btn-download" onclick="downloadPDF()" style="background: #10b981;"><span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">download</span> Muat Turun PDF</button>';

        if (props.status === 'pending') {
            actionsDiv.innerHTML = `
                <button class="btn btn-approve" onclick="updateStatus('approved')">Luluskan</button>
                <button class="btn btn-reject" onclick="updateStatus('rejected')">Tolak</button>
                ${downloadBtn}
            `;
            actionsDiv.style.display = 'flex';
        } else if (props.status === 'approved') {
            actionsDiv.innerHTML = `
                <button class="btn btn-complete" onclick="updateStatus('completed')">Tandakan Selesai</button>
                ${downloadBtn}
            `;
            actionsDiv.style.display = 'flex';
        } else {
            actionsDiv.innerHTML = downloadBtn;
            actionsDiv.style.display = 'flex';
        }

        document.getElementById('bookingModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('bookingModal').classList.remove('show');
        currentBookingId = null;
        document.getElementById('statusNotes').value = '';
        document.getElementById('notesSection').style.display = 'none';
    }

    async function updateStatus(status) {
        if (!currentBookingId) return;

        // Show notes section
        document.getElementById('notesSection').style.display = 'block';

        const notes = document.getElementById('statusNotes').value;

        try {
            const response = await fetch(`/admin/bookings/${currentBookingId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ status, notes })
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                closeModal();
                calendar.refetchEvents(); // Reload events
            } else {
                alert('Ralat: ' + (result.message || 'Gagal mengemas kini status'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ralat semasa mengemas kini status');
        }
    }

    function formatDateTime(date) {
        if (!date) return '-';
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        const hours = String(d.getHours()).padStart(2, '0');
        const minutes = String(d.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year} ${hours}:${minutes}`;
    }

    // Download booking as PDF
    function downloadPDF() {
        if (!currentBookingId) {
            alert('Tiada tempahan dipilih');
            return;
        }

        // Open PDF in new tab for download
        window.open(`{{ url('/admin/bookings') }}/${currentBookingId}/pdf`, '_blank');
    }

    // Close modal when clicking outside
    document.getElementById('bookingModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush
