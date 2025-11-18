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

        return view('staff.dashboard', compact('eventBanners'));
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

        return redirect()->route('staff.profile')->with('success', 'Profil berjaya dikemaskini');
    }

    /**
     * Upload profile photo.
     */
    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            // Store new photo
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'photo_url' => Storage::url($path),
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
