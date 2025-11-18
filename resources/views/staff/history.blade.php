@extends('layouts.staff')

@section('title', 'Sejarah Tempahan')

@section('content')
<div style="background: rgba(255,255,255,0.95); border-radius: 15px; padding: 20px; box-shadow: 0px 4px 10px rgba(0,0,0,0.2);">
  <h2>Sejarah Tempahan</h2>

  @if(isset($bookingHistory) && count($bookingHistory) > 0)
    <table style="width: 100%; margin-top: 20px; border-collapse: collapse;">
      <thead>
        <tr style="border-bottom: 2px solid #ddd;">
          <th style="padding: 10px; text-align: left;">Tarikh</th>
          <th style="padding: 10px; text-align: left;">Kenderaan</th>
          <th style="padding: 10px; text-align: left;">Status</th>
          <th style="padding: 10px; text-align: left;">Catatan</th>
        </tr>
      </thead>
      <tbody>
        @foreach($bookingHistory as $history)
          <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 10px;">{{ $history->created_at->format('d/m/Y') }}</td>
            <td style="padding: 10px;">{{ $history->vehicle_name }}</td>
            <td style="padding: 10px;">
              <span style="
                padding: 4px 12px;
                border-radius: 12px;
                font-size: 0.875rem;
                font-weight: 600;
                display: inline-block;
                {{ $history->status == 'approved' ? 'background-color: #dcfce7; color: #16a34a;' : '' }}
                {{ $history->status == 'rejected' ? 'background-color: #fee2e2; color: #dc2626;' : '' }}
                {{ $history->status == 'pending' ? 'background-color: #fef3c7; color: #d97706;' : '' }}
                {{ $history->status == 'completed' ? 'background-color: #dbeafe; color: #2563eb;' : '' }}
                {{ $history->status == 'cancelled' ? 'background-color: #f3f4f6; color: #6b7280;' : '' }}
              ">
                {{ ucfirst($history->status) }}
              </span>
            </td>
            <td style="padding: 10px;">{{ $history->notes ?? '-' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p style="margin-top: 15px; color: #666;">Tiada sejarah tempahan.</p>
  @endif
</div>
@endsection
