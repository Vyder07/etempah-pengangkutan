@extends('layouts.staff')

@section('title', 'Tempahan Kenderaan')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
  .fc {
    background: white;
    border-radius: 12px;
    padding: 15px;
  }

  .fc-toolbar-title {
    font-size: 1.5rem !important;
    font-weight: 600 !important;
  }

  .fc-event {
    cursor: pointer;
    border: none !important;
    padding: 2px 4px;
  }

  .fc-event-pending {
    background-color: #f59e0b !important;
  }

  .fc-event-approved {
    background-color: #10b981 !important;
  }

  .fc-event-rejected {
    background-color: #ef4444 !important;
  }

  .fc-event-completed {
    background-color: #3b82f6 !important;
  }

  .fc-daygrid-day:hover {
    background-color: #f3f4f6;
    cursor: pointer;
  }

  .vehicle-type-card {
    cursor: pointer;
    transition: all 0.3s;
  }

  .vehicle-type-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  }

  .vehicle-type-card.selected {
    border: 3px solid #3b82f6;
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
  }

  /* Toast Notification */
  .toast {
    position: fixed;
    top: 80px;
    right: 20px;
    background: #10b981;
    color: black;
    padding: 16px 24px;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 99999;
    animation: slideIn 0.3s ease-out;
    min-width: 320px;
    max-width: 500px;
    font-family: 'Poppins', sans-serif;
  }

  .toast.error {
    background: #ef4444;
  }

  .toast.hide {
    animation: slideOut 0.3s ease-out forwards;
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
<div class="space-y-6">
  <!-- Header -->
  <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2 flex items-center gap-2">
          <span class="material-symbols-outlined text-blue-600">car_rental</span>
          Tempahan Kenderaan
        </h1>
        <p class="text-gray-600">Klik pada tarikh kalendar untuk membuat tempahan baharu</p>
      </div>
      <div class="flex gap-3">
        <button onclick="openBookingModal()" class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition shadow-lg">
          <span class="material-symbols-outlined">add</span>
          Tempahan Baharu
        </button>
      </div>
    </div>
  </div>

  <!-- Calendar -->
  <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-6">
    <div id="calendar"></div>
  </div>

  <!-- My Bookings List -->
  <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
      <span class="material-symbols-outlined text-blue-600">list</span>
      Tempahan Saya
    </h2>

    @if(isset($bookings) && count($bookings) > 0)
      <div class="space-y-4">
        @foreach($bookings as $booking)
          <div class="border border-gray-200 rounded-xl p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                  <span class="material-symbols-outlined text-blue-600 text-3xl">directions_car</span>
                  <div>
                    <h3 class="font-semibold text-lg text-gray-800">{{ $booking->vehicle_name ?? 'N/A' }}</h3>
                    <p class="text-sm text-gray-500">{{ $booking->vehicle_plate ?? 'N/A' }}</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                  <div class="flex items-center gap-2 text-gray-600">
                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                    <span class="text-sm">Mula: {{ $booking->start_date->format('d/m/Y H:i') }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-600">
                    <span class="material-symbols-outlined text-sm">event</span>
                    <span class="text-sm">Tamat: {{ $booking->end_date->format('d/m/Y H:i') }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-600">
                    <span class="material-symbols-outlined text-sm">location_on</span>
                    <span class="text-sm">{{ $booking->destination }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-600">
                    <span class="material-symbols-outlined text-sm">category</span>
                    <span class="text-sm">{{ ucfirst($booking->vehicle_type ?? 'N/A') }}</span>
                  </div>
                </div>

                <div class="flex items-start gap-2 text-gray-600 mb-2">
                  <span class="material-symbols-outlined text-sm mt-0.5">description</span>
                  <span class="text-sm">{{ $booking->purpose }}</span>
                </div>

                @if($booking->notes)
                  <div class="flex items-start gap-2 text-gray-500 text-sm">
                    <span class="material-symbols-outlined text-sm mt-0.5">note</span>
                    <span>Nota: {{ $booking->notes }}</span>
                  </div>
                @endif
              </div>

              <div class="ml-4 flex flex-col items-end gap-2">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                  @if($booking->status === 'pending') bg-amber-100 text-amber-800
                  @elseif($booking->status === 'approved') bg-green-100 text-green-800
                  @elseif($booking->status === 'rejected') bg-red-100 text-red-800
                  @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                  @else bg-gray-100 text-gray-800
                  @endif">
                  {{ ucfirst($booking->status) }}
                </span>

                @if($booking->status === 'pending')
                  <button onclick="editBooking({{ $booking->id }})"
                          class="flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    Ubah
                  </button>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="text-center py-12">
        <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">inbox</span>
        <p class="text-gray-500 text-lg">Tiada tempahan pada masa ini</p>
      </div>
    @endif
  </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="hidden fixed inset-0 bg-black/60 z-50 overflow-y-auto">
  <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
      <!-- Modal Header -->
      <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <h3 id="modalTitle" class="text-2xl font-bold flex items-center gap-2">
            <span class="material-symbols-outlined">add_circle</span>
            Tempahan Baharu
          </h3>
          <button onclick="closeBookingModal()" class="text-white hover:bg-white/20 rounded-full p-2 transition">
            <span class="material-symbols-outlined">close</span>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <form id="bookingForm" class="p-6 space-y-6">
        @csrf
        <input type="hidden" id="bookingId" name="booking_id">

        <!-- Vehicle Type Selection -->
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-3">Jenis Kenderaan *</label>
          <div class="grid grid-cols-3 gap-4">
            <div class="vehicle-type-card border-2 border-gray-300 rounded-xl p-4 text-center" data-type="car">
              <span class="material-symbols-outlined text-4xl text-blue-600 mb-2">directions_car</span>
              <p class="font-semibold text-gray-800">Kereta</p>
            </div>
            <div class="vehicle-type-card border-2 border-gray-300 rounded-xl p-4 text-center" data-type="van">
              <span class="material-symbols-outlined text-4xl text-green-600 mb-2">airport_shuttle</span>
              <p class="font-semibold text-gray-800">Van</p>
            </div>
            <div class="vehicle-type-card border-2 border-gray-300 rounded-xl p-4 text-center" data-type="bus">
              <span class="material-symbols-outlined text-4xl text-purple-600 mb-2">directions_bus</span>
              <p class="font-semibold text-gray-800">Bus</p>
            </div>
          </div>
          <input type="hidden" id="vehicleType" name="vehicle_type" required>
          <span class="text-red-500 text-sm hidden" id="vehicleTypeError">Sila pilih jenis kenderaan</span>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="startDate" class="block text-sm font-semibold text-gray-700 mb-2">Tarikh & Masa Mula *</label>
            <input type="datetime-local" id="startDate" name="start_date" required
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label for="endDate" class="block text-sm font-semibold text-gray-700 mb-2">Tarikh & Masa Tamat *</label>
            <input type="datetime-local" id="endDate" name="end_date" required
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
        </div>

        <!-- Vehicle Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="vehicleName" class="block text-sm font-semibold text-gray-700 mb-2">Nama Kenderaan *</label>
            <input type="text" id="vehicleName" name="vehicle_name" required placeholder="Cth: Proton Saga"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
          <div>
            <label for="vehiclePlate" class="block text-sm font-semibold text-gray-700 mb-2">No. Pendaftaran *</label>
            <input type="text" id="vehiclePlate" name="vehicle_plate" required placeholder="Cth: ABC1234"
                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
          </div>
        </div>

        <!-- Destination -->
        <div>
          <label for="destination" class="block text-sm font-semibold text-gray-700 mb-2">Destinasi *</label>
          <input type="text" id="destination" name="destination" required placeholder="Cth: Kuala Lumpur"
                 class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <!-- Purpose -->
        <div>
          <label for="purpose" class="block text-sm font-semibold text-gray-700 mb-2">Tujuan *</label>
          <textarea id="purpose" name="purpose" required rows="3" placeholder="Nyatakan tujuan tempahan..."
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
        </div>

        <!-- Submit Buttons -->
        <div class="flex gap-4 pt-4">
          <button type="button" onclick="closeBookingModal()"
                  class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
            Batal
          </button>
          <button type="submit"
                  class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition shadow-lg">
            Hantar Tempahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
  let calendar;
  let isEditMode = false;
  let editingBookingId = null;

  // Bookings data for edit functionality
  const bookingsData = {!! json_encode($bookings->map(function($booking) {
    return [
      'id' => $booking->id,
      'vehicle_type' => $booking->vehicle_type,
      'vehicle_name' => $booking->vehicle_name,
      'vehicle_plate' => $booking->vehicle_plate,
      'destination' => $booking->destination,
      'purpose' => $booking->purpose,
      'start_date' => $booking->start_date->format('Y-m-d\TH:i'),
      'end_date' => $booking->end_date->format('Y-m-d\TH:i'),
      'status' => $booking->status,
    ];
  })->keyBy('id')) !!};

  // Toast Notification Function
  function showToast(title, message, type = 'success') {
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
      existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    const icon = type === 'success' ? '✓' : '✕';

    toast.innerHTML = `
      <span class="text-2xl">${icon}</span>
      <div class="flex-1">
        <div class="font-semibold">${title}</div>
        <div class="text-sm opacity-90">${message}</div>
      </div>
      <button class="text-white hover:text-gray-200" onclick="this.parentElement.remove()">×</button>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('hide');
      setTimeout(() => toast.remove(), 300);
    }, 5000);
  }

  // Initialize Calendar
  document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
      },
      height: 'auto',
      events: {!! json_encode($bookings->map(function($booking) {
        return [
          'id' => $booking->id,
          'title' => $booking->vehicle_name,
          'start' => $booking->start_date->toIso8601String(),
          'end' => $booking->end_date->toIso8601String(),
          'className' => 'fc-event-' . $booking->status,
          'extendedProps' => [
            'vehicle_plate' => $booking->vehicle_plate,
            'destination' => $booking->destination,
            'purpose' => $booking->purpose,
            'status' => $booking->status,
            'vehicle_type' => $booking->vehicle_type ?? 'N/A',
          ],
        ];
      })) !!},
      dateClick: function(info) {
        openBookingModal(info.dateStr);
      },
      eventClick: function(info) {
        const event = info.event;
        const bookingId = event.id;
        const props = event.extendedProps;

        // If status is pending, open edit modal
        if (props.status === 'pending') {
          editBooking(bookingId);
        } else {
          // For non-pending bookings, show details in alert
          alert(
            `Kenderaan: ${event.title}\n` +
            `No. Pendaftaran: ${props.vehicle_plate}\n` +
            `Jenis: ${props.vehicle_type}\n` +
            `Destinasi: ${props.destination}\n` +
            `Tujuan: ${props.purpose}\n` +
            `Status: ${props.status.toUpperCase()}\n` +
            `Mula: ${event.start.toLocaleString('ms-MY')}\n` +
            `Tamat: ${event.end ? event.end.toLocaleString('ms-MY') : 'N/A'}`
          );
        }
      }
    });

    calendar.render();
  });

  // Open Booking Modal for creating new booking
  function openBookingModal(date = null) {
    isEditMode = false;
    editingBookingId = null;

    // Update modal title
    document.getElementById('modalTitle').innerHTML = `
      <span class="material-symbols-outlined">add_circle</span>
      Tempahan Baharu
    `;

    const modal = document.getElementById('bookingModal');
    modal.classList.remove('hidden');

    // Set default dates
    if (date) {
      const startDate = new Date(date);
      startDate.setHours(8, 0); // Default 8 AM
      document.getElementById('startDate').value = formatDateTimeLocal(startDate);

      const endDate = new Date(date);
      endDate.setHours(17, 0); // Default 5 PM
      document.getElementById('endDate').value = formatDateTimeLocal(endDate);
    } else {
      const now = new Date();
      document.getElementById('startDate').value = formatDateTimeLocal(now);

      const endTime = new Date(now);
      endTime.setHours(now.getHours() + 2);
      document.getElementById('endDate').value = formatDateTimeLocal(endTime);
    }
  }

  // Open Booking Modal for editing existing booking
  function editBooking(bookingId) {
    const booking = bookingsData[bookingId];
    if (!booking) {
      showToast('Ralat', 'Tempahan tidak dijumpai', 'error');
      return;
    }

    isEditMode = true;
    editingBookingId = bookingId;

    // Update modal title
    document.getElementById('modalTitle').innerHTML = `
      <span class="material-symbols-outlined">edit</span>
      Ubah Tempahan
    `;

    const modal = document.getElementById('bookingModal');
    modal.classList.remove('hidden');

    // Populate form with booking data
    document.getElementById('bookingId').value = booking.id;
    document.getElementById('startDate').value = booking.start_date;
    document.getElementById('endDate').value = booking.end_date;
    document.getElementById('vehicleName').value = booking.vehicle_name;
    document.getElementById('vehiclePlate').value = booking.vehicle_plate;
    document.getElementById('destination').value = booking.destination;
    document.getElementById('purpose').value = booking.purpose;

    // Set vehicle type
    document.querySelectorAll('.vehicle-type-card').forEach(card => {
      card.classList.remove('selected');
      if (card.dataset.type === booking.vehicle_type) {
        card.classList.add('selected');
      }
    });
    document.getElementById('vehicleType').value = booking.vehicle_type;
  }

  // Close Booking Modal
  function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    modal.classList.add('hidden');
    document.getElementById('bookingForm').reset();

    // Reset edit mode
    isEditMode = false;
    editingBookingId = null;
    document.getElementById('bookingId').value = '';

    // Remove selected class from vehicle types
    document.querySelectorAll('.vehicle-type-card').forEach(card => {
      card.classList.remove('selected');
    });
    document.getElementById('vehicleType').value = '';
    document.getElementById('vehicleTypeError').classList.add('hidden');
  }

  // Format datetime for input
  function formatDateTimeLocal(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  }

  // Vehicle Type Selection
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.vehicle-type-card').forEach(card => {
      card.addEventListener('click', function() {
        // Remove selected from all
        document.querySelectorAll('.vehicle-type-card').forEach(c => {
          c.classList.remove('selected');
        });

        // Add selected to clicked
        this.classList.add('selected');

        // Set hidden input value
        const type = this.dataset.type;
        document.getElementById('vehicleType').value = type;
        document.getElementById('vehicleTypeError').classList.add('hidden');
      });
    });
  });

  // Form Submission
  document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    // Validate vehicle type
    const vehicleType = document.getElementById('vehicleType').value;
    if (!vehicleType) {
      document.getElementById('vehicleTypeError').classList.remove('hidden');
      showToast('Ralat', 'Sila pilih jenis kenderaan', 'error');
      return;
    }

    // Validate dates
    const startDate = new Date(document.getElementById('startDate').value);
    const endDate = new Date(document.getElementById('endDate').value);

    if (endDate <= startDate) {
      showToast('Ralat', 'Tarikh tamat mesti selepas tarikh mula', 'error');
      return;
    }

    // Get form data
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    // Disable button
    submitBtn.disabled = true;
    submitBtn.textContent = isEditMode ? 'Mengemaskini...' : 'Menghantar...';

    try {
      // Determine URL and method based on edit mode
      let url, method;
      if (isEditMode && editingBookingId) {
        url = `/staff/booking/${editingBookingId}`;
        method = 'PUT';
      } else {
        url = '{{ route("staff.booking.store") }}';
        method = 'POST';
      }

      // Convert FormData to JSON for PUT request
      const requestBody = method === 'PUT' ? JSON.stringify(Object.fromEntries(formData)) : formData;
      const headers = {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      };

      // Add Content-Type for PUT request
      if (method === 'PUT') {
        headers['Content-Type'] = 'application/json';
      }

      const response = await fetch(url, {
        method: method,
        headers: headers,
        body: requestBody
      });

      const data = await response.json();

      if (data.success) {
        showToast('Berjaya!', data.message || (isEditMode ? 'Tempahan berjaya dikemaskini' : 'Tempahan berjaya dihantar'), 'success');
        closeBookingModal();

        // Reload page after 2 seconds
        setTimeout(() => {
          window.location.reload();
        }, 2000);
      } else {
        showToast('Ralat!', data.message || 'Gagal memproses tempahan', 'error');
      }
    } catch (error) {
      console.error('Error:', error);
      showToast('Ralat!', 'Ralat berlaku. Sila cuba lagi.', 'error');
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
    }
  });
</script>
@endpush
