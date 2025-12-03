@extends('admin.layouts.app')

@section('title', 'Notifikasi Admin')

@section('content')
<main class="w-full p-6">
    <div class="bg-white shadow-lg rounded-xl p-6 w-full">
        <h2 class="text-xl font-bold mb-4">Senarai Notifikasi Permohonan</h2>

        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="py-2 text-left">Nama Pemohon</th>
                    <th class="py-2 text-left">Status</th>
                    <th class="py-2 text-center">Tindakan</th>
                    <th class="py-2 text-left">Tarikh Permohonan</th>
                    <th class="py-2 text-center">Padam</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr class="border-b" data-booking-id="{{ $booking->id }}">
                    <td class="py-3 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold">
                            {{ substr($booking->user->name, 0, 1) }}
                        </div>
                        {{ $booking->user->name }}
                    </td>
                    <td>
                        <span class="status-badge {{ $booking->status_color }} font-semibold px-3 py-1 rounded">
                            {{ strtoupper($booking->status_label) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <button
                            onclick="openModal({{ $booking->id }}, '{{ $booking->user->name }}', '{{ $booking->user->department ?? 'N/A' }}', '{{ $booking->created_at->format('Y-m-d') }}', '{{ $booking->memo_file ?? '' }}', '{{ addslashes($booking->purpose) }}', '{{ $booking->vehicle_name }}', '{{ $booking->destination }}', '{{ $booking->start_date->format('Y-m-d') }}', '{{ $booking->end_date->format('Y-m-d') }}', '{{ $booking->status }}', this)"
                            class="text-blue-600 hover:text-blue-800 text-xl">
                            👁
                        </button>
                    </td>
                    <td>{{ $booking->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <button
                            onclick="deleteBooking({{ $booking->id }}, this)"
                            class="text-red-600 hover:text-red-800 text-xl"
                            title="Padam">
                            🗑️
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-8 text-gray-500">
                        Tiada notifikasi permohonan
                    </td>
                </tr>
                @endforelse
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
            <div>
                <label class="font-bold">Destinasi:</label>
                <input id="modalDestinasi" class="w-full border rounded px-2 py-1 bg-gray-50 cursor-default" readonly>
            </div>
            <div>
                <label class="font-bold">Tempoh:</label>
                <input id="modalTempoh" class="w-full border rounded px-2 py-1 bg-gray-50 cursor-default" readonly>
            </div>
            <div class="col-span-2">
                <label class="font-bold">Memo:</label>
                <div class="flex items-center gap-2 bg-gray-50 border rounded px-2 py-1">
                    <span id="modalMemo" class="flex-1 text-gray-700 select-none">-</span>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <label class="font-bold">Maklumat Permohonan:</label>
            <textarea id="modalMaklumat" class="w-full border rounded px-2 py-2 bg-gray-50 cursor-default" rows="4" readonly></textarea>
        </div>

        <div id="actionButtons" class="flex gap-4 justify-center">
            <button id="approveBtn" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition">LULUSKAN</button>
            <button id="rejectBtn" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 transition">TOLAK</button>
        </div>

        <div id="statusMessage" class="hidden mt-4 text-center font-semibold"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentBookingId = null;
    let currentRow = null;

    function openModal(bookingId, nama, bahagian, tarikh, memo, maklumat, kenderaan, destinasi, startDate, endDate, status, btn) {
        currentBookingId = bookingId;
        currentRow = btn.closest("tr");

        document.getElementById('detailModal').classList.remove('hidden');
        document.getElementById('modalNama').value = nama;
        document.getElementById('modalBahagian').value = bahagian;
        document.getElementById('modalTarikh').value = tarikh;
        document.getElementById('modalMemo').innerText = memo || 'Tiada memo';
        document.getElementById('modalMaklumat').value = maklumat;
        document.getElementById('modalKenderaan').value = kenderaan;
        document.getElementById('modalDestinasi').value = destinasi;
        document.getElementById('modalTempoh').value = startDate + ' hingga ' + endDate;

        // Hide action buttons if already approved/rejected/completed/cancelled
        const actionButtons = document.getElementById('actionButtons');
        const statusMessage = document.getElementById('statusMessage');

        if (status === 'approved' || status === 'rejected' || status === 'completed' || status === 'cancelled') {
            actionButtons.classList.add('hidden');
            statusMessage.classList.remove('hidden');
            statusMessage.textContent = 'Permohonan ini telah ' + (status === 'approved' ? 'diluluskan' : status === 'rejected' ? 'ditolak' : status === 'completed' ? 'selesai' : 'dibatalkan');
            statusMessage.className = 'mt-4 text-center font-semibold ' +
                (status === 'approved' ? 'text-green-600' : status === 'rejected' ? 'text-red-600' : 'text-gray-600');
        } else {
            actionButtons.classList.remove('hidden');
            statusMessage.classList.add('hidden');
        }
    }

    function closeModal() {
        document.getElementById('detailModal').classList.add('hidden');
        currentBookingId = null;
        currentRow = null;
    }

    async function updateBookingStatus(status) {
        if (!currentBookingId) return;

        try {
            const response = await fetch(`/admin/notifications/${currentBookingId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status })
            });

            const data = await response.json();

            if (data.success) {
                // Update the status badge in the table
                if (currentRow) {
                    const statusBadge = currentRow.querySelector('.status-badge');
                    statusBadge.textContent = data.status_label.toUpperCase();
                    statusBadge.className = 'status-badge ' + data.status_color + ' font-semibold px-3 py-1 rounded';
                }

                // Show success message briefly
                const statusMessage = document.getElementById('statusMessage');
                statusMessage.classList.remove('hidden');
                statusMessage.textContent = data.message;
                statusMessage.className = 'mt-4 text-center font-semibold text-green-600';

                // Hide action buttons
                document.getElementById('actionButtons').classList.add('hidden');

                // Close modal after 1.5 seconds
                setTimeout(() => {
                    closeModal();
                }, 1500);
            } else {
                alert('Ralat: ' + (data.message || 'Gagal mengemaskini status'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ralat sambungan. Sila cuba lagi.');
        }
    }

    async function deleteBooking(bookingId, btn) {
        if (!confirm('Adakah anda pasti untuk memadam permohonan ini? Tindakan ini tidak boleh dibatalkan.')) {
            return;
        }

        try {
            const response = await fetch(`/admin/notifications/${bookingId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                // Remove the row from the table with animation
                const row = btn.closest('tr');
                row.style.transition = 'opacity 0.3s';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();

                    // Check if table is empty
                    const tbody = document.querySelector('table tbody');
                    if (tbody.children.length === 0) {
                        tbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-8 text-gray-500">
                                    Tiada notifikasi permohonan
                                </td>
                            </tr>
                        `;
                    }
                }, 300);

                alert('Permohonan berjaya dipadam.');
            } else {
                alert('Ralat: ' + (data.message || 'Gagal memadam permohonan'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Ralat sambungan. Sila cuba lagi.');
        }
    }

    document.getElementById("approveBtn").addEventListener("click", function() {
        if (confirm('Adakah anda pasti untuk meluluskan permohonan ini?')) {
            updateBookingStatus('approved');
        }
    });

    document.getElementById("rejectBtn").addEventListener("click", function() {
        if (confirm('Adakah anda pasti untuk menolak permohonan ini?')) {
            updateBookingStatus('rejected');
        }
    });

    // Close modal when clicking outside
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endpush
