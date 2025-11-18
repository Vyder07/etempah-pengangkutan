@extends('layouts.staff')

@section('title', 'Tempahan')

@section('content')
<div style="background: rgba(255,255,255,0.95); border-radius: 15px; padding: 20px; box-shadow: 0px 4px 10px rgba(0,0,0,0.2);">
  <h2>Tempahan</h2>
  <p>Halaman tempahan untuk staff.</p>
  
  @if(isset($bookings) && count($bookings) > 0)
    <div class="bookings-list">
      @foreach($bookings as $booking)
        <div class="booking-item" style="padding: 15px; border-bottom: 1px solid #eee;">
          <p><strong>Tarikh:</strong> {{ $booking->booking_date }}</p>
          <p><strong>Status:</strong> {{ $booking->status }}</p>
        </div>
      @endforeach
    </div>
  @else
    <p style="margin-top: 15px; color: #666;">Tiada tempahan pada masa ini.</p>
  @endif
</div>
@endsection
