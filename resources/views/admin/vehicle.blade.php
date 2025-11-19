@extends('admin.layouts.app')

@section('title', 'Senarai Tempahan')

@section('content')
<main class="w-full p-6">
    <div class="bg-white rounded-xl shadow-md p-5 mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Senarai Tempahan Kenderaan</h1>
            <span class="text-sm text-gray-500">{{ $bookings->total() }} tempahan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-sm text-gray-600 border-b">
                        <th class="py-3 px-2">ID</th>
                        <th class="py-3 px-2">Kenderaan</th>
                        <th class="py-3 px-2">Pengguna</th>
                        <th class="py-3 px-2">Destinasi</th>
                        <th class="py-3 px-2">Tarikh</th>
                        <th class="py-3 px-2 text-center">Status</th>
                        <th class="py-3 px-2 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-b hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-2">
                            <span class="font-mono text-sm">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-3 px-2">
                            <div class="flex flex-col">
                                <div class="font-medium text-gray-800">{{ $booking->vehicle_name }}</div>
                                <div class="text-xs text-gray-500">{{ strtoupper($booking->vehicle_type) }} - {{ $booking->vehicle_plate }}</div>
                            </div>
                        </td>
                        <td class="py-3 px-2">
                            <div class="text-sm text-gray-700">{{ $booking->user->name }}</div>
                        </td>
                        <td class="py-3 px-2">
                            <div class="flex flex-col">
                                <div class="text-sm text-gray-800">{{ $booking->destination }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->purpose }}</div>
                            </div>
                        </td>
                        <td class="py-3 px-2">
                            <div class="text-xs">
                                <div>{{ $booking->start_date->format('d/m/Y') }}</div>
                                <div class="text-gray-500">hingga</div>
                                <div>{{ $booking->end_date->format('d/m/Y') }}</div>
                            </div>
                        </td>
                        <td class="py-3 px-2 text-center">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $colorClass = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $colorClass }}">
                                {{ $booking->status_label }}
                            </span>
                        </td>
                        <td class="py-3 px-2">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('admin.bookings.downloadPdf', $booking->id) }}"
                                   target="_blank"
                                   class="cursor-pointer text-base p-2 rounded inline-flex items-center justify-center bg-green-600 text-white hover:bg-green-700 transition-colors"
                                   title="Muat Turun PDF">
                                    <span class="material-icons text-lg">download</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <span class="material-icons text-5xl opacity-50">event_busy</span>
                                <p>Tiada tempahan dijumpai</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</main>
@endsection
