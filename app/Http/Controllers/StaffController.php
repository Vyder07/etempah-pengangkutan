<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventBanner;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StaffController extends Controller
{
    /**
     * Display the staff dashboard.
     */
    public function dashboard()
    {
        $eventBanners = EventBanner::getActiveBanners();

        // Booking statistics for current user
        $totalBookings = Booking::where('user_id', Auth::id())->count();
        $pendingBookings = Booking::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->count();
        $completedToday = Booking::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereDate('end_date', Carbon::today())
            ->count();

        return view('staff.dashboard', compact(
            'eventBanners',
            'totalBookings',
            'pendingBookings',
            'completedToday'
        ));
    }

    /**
     * Display the booking page.
     */
    public function booking()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.booking', compact('bookings'));
    }

    /**
     * Store a new booking.
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'vehicle_type' => 'required|in:car,van,bus',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'vehicle_name' => 'required|string|max:255',
            'vehicle_plate' => 'required|string|max:20',
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string|max:1000',
        ]);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'vehicle_type' => $request->vehicle_type,
            'vehicle_name' => $request->vehicle_name,
            'vehicle_plate' => $request->vehicle_plate,
            'destination' => $request->destination,
            'purpose' => $request->purpose,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending',
        ]);

        // Broadcast notification to admin via WebSocket
        broadcast(new \App\Events\BookingCreated($booking))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Tempahan berjaya dihantar. Sila tunggu kelulusan admin.',
            'booking' => $booking,
        ]);
    }

    /**
     * Update an existing booking.
     */
    public function updateBooking(Request $request, $id)
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Only allow editing pending bookings
        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya tempahan yang masih menunggu kelulusan boleh diubah.',
            ], 403);
        }

        $request->validate([
            'vehicle_type' => 'required|in:car,van,bus',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'vehicle_name' => 'required|string|max:255',
            'vehicle_plate' => 'required|string|max:20',
            'destination' => 'required|string|max:255',
            'purpose' => 'required|string|max:1000',
        ]);

        $booking->update([
            'vehicle_type' => $request->vehicle_type,
            'vehicle_name' => $request->vehicle_name,
            'vehicle_plate' => $request->vehicle_plate,
            'destination' => $request->destination,
            'purpose' => $request->purpose,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // Broadcast notification to admin via WebSocket
        broadcast(new \App\Events\BookingUpdated($booking))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Tempahan berjaya dikemaskini.',
            'booking' => $booking,
        ]);
    }

    /**
     * Display the notification page.
     */
    public function notification()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.notification', compact('bookings'));
    }

    /**
     * Display the booking history page.
     */
    public function history()
    {
        $bookingHistory = Booking::where('user_id', Auth::id())
            ->whereIn('status', ['approved', 'rejected', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('staff.history', compact('bookingHistory'));
    }

    /**
     * Display the profile page.
     */
    public function profile()
    {
        return view('staff.profile');
    }

    /**
     * Update the staff profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil berjaya dikemaskini',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        return redirect()->route('staff.profile')->with('success', 'Profil berjaya dikemaskini');
    }

        /**
     * Upload profile photo
     */
    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            // Clear existing profile photo (Spatie will handle deletion)
            $user->clearMediaCollection('profile_photo');

            // Add new profile photo to media library
            $media = $user->addMediaFromRequest('profile_photo')
                ->usingFileName(time() . '_profile.' . $request->file('profile_photo')->extension())
                ->toMediaCollection('profile_photo');

            return response()->json([
                'success' => true,
                'photo_url' => $user->getFirstMediaUrl('profile_photo', 'profile'),
                'message' => 'Gambar profil berjaya dikemas kini',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tiada fail gambar dijumpai',
        ], 400);
    }

    /**
     * Display the logout confirmation page.
     */
    public function logoutPage()
    {
        return view('staff.logout');
    }
}
