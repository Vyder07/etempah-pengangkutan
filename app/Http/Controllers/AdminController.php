<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventBanner;
use App\Models\Booking;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $banners = EventBanner::getActiveBanners();
        return view('admin.dashboard', compact('banners'));
    }

    /**
     * Store a new event banner
     */
    public function storeBanner(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|string', // base64 image
        ]);

        // Get next order number
        $maxOrder = EventBanner::max('order') ?? 0;

        // Create banner record
        $banner = EventBanner::create([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        // Handle base64 image upload with Spatie Media Library
        if ($request->has('image') && $request->image) {
            // Decode base64 image
            $imageData = $request->image;

            // Remove data URI prefix if present
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $extension = $matches[1];
            } else {
                $extension = 'jpg';
            }

            $imageData = str_replace(' ', '+', $imageData);
            $imageDecoded = base64_decode($imageData);

            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'banner_');
            file_put_contents($tempFile, $imageDecoded);

            // Add to media library
            $banner->addMedia($tempFile)
                ->usingFileName('banner_' . time() . '_' . Str::random(8) . '.' . $extension)
                ->toMediaCollection('banner');

            // Clean up temp file
            @unlink($tempFile);
        }

        // Reload with media
        $banner->load('media');

        return response()->json([
            'success' => true,
            'message' => 'Banner berjaya dimuat naik',
            'banner' => $banner,
            'banner_url' => $banner->banner_url,
        ]);
    }

    /**
     * Update an existing event banner
     */
    public function updateBanner(Request $request, $id)
    {
        $banner = EventBanner::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string', // base64 image (optional)
        ]);

        // Update text fields
        $banner->title = $request->title;
        $banner->description = $request->description;
        $banner->save();

        // Update image if provided
        if ($request->has('image') && $request->image) {
            // Decode base64 image
            $imageData = $request->image;

            // Remove data URI prefix if present
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $extension = $matches[1];
            } else {
                $extension = 'jpg';
            }

            $imageData = str_replace(' ', '+', $imageData);
            $imageDecoded = base64_decode($imageData);

            // Create temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'banner_');
            file_put_contents($tempFile, $imageDecoded);

            // Clear existing media and add new one
            $banner->clearMediaCollection('banner');
            $banner->addMedia($tempFile)
                ->usingFileName('banner_' . time() . '_' . Str::random(8) . '.' . $extension)
                ->toMediaCollection('banner');

            // Clean up temp file
            @unlink($tempFile);
        }

        // Reload with media
        $banner->load('media');

        return response()->json([
            'success' => true,
            'message' => 'Banner berjaya dikemas kini',
            'banner' => $banner,
            'banner_url' => $banner->banner_url,
        ]);
    }

    /**
     * Delete an event banner
     */
    public function deleteBanner($id)
    {
        $banner = EventBanner::findOrFail($id);

        // Media will be automatically deleted when banner is deleted
        // due to Spatie's cascade delete
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner berjaya dipadam',
        ]);
    }

    /**
     * Reorder banners
     */
    public function reorderBanners(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:event_banners,id',
            'banners.*.order' => 'required|integer',
        ]);

        foreach ($request->banners as $bannerData) {
            EventBanner::where('id', $bannerData['id'])
                ->update(['order' => $bannerData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Susunan banner berjaya dikemas kini',
        ]);
    }

    /**
     * Display the booking calendar page.
     */
    public function booking(Request $request)
    {
        $view = $request->get('view', 'month'); // month, week, day
        $date = $request->get('date', now()->format('Y-m-d'));
        
        $currentDate = Carbon::parse($date);
        
        // Fetch bookings based on view
        $bookings = Booking::with('user')
            ->when($view === 'month', function ($query) use ($currentDate) {
                return $query->whereBetween('start_date', [
                    $currentDate->copy()->startOfMonth(),
                    $currentDate->copy()->endOfMonth()
                ]);
            })
            ->when($view === 'week', function ($query) use ($currentDate) {
                return $query->whereBetween('start_date', [
                    $currentDate->copy()->startOfWeek(),
                    $currentDate->copy()->endOfWeek()
                ]);
            })
            ->when($view === 'day', function ($query) use ($currentDate) {
                return $query->whereDate('start_date', $currentDate);
            })
            ->orderBy('start_date')
            ->get();
        
        return view('admin.booking', compact('bookings', 'view', 'currentDate'));
    }

    /**
     * Get bookings as JSON for calendar
     */
    public function getBookings(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        
        $bookings = Booking::with('user')
            ->whereBetween('start_date', [$start, $end])
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'title' => $booking->vehicle_name,
                    'start' => $booking->start_date->toIso8601String(),
                    'end' => $booking->end_date->toIso8601String(),
                    'color' => $this->getEventColor($booking->status),
                    'extendedProps' => [
                        'user' => $booking->user->name,
                        'vehicle_plate' => $booking->vehicle_plate,
                        'destination' => $booking->destination,
                        'purpose' => $booking->purpose,
                        'status' => $booking->status,
                        'status_label' => $booking->status_label,
                        'notes' => $booking->notes,
                    ]
                ];
            });
        
        return response()->json($bookings);
    }

    /**
     * Get single booking details
     */
    public function getBooking($id)
    {
        $booking = Booking::with('user')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'booking' => $booking,
        ]);
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $booking->status = $request->status;
        if ($request->has('notes')) {
            $booking->notes = $request->notes;
        }
        $booking->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status tempahan berjaya dikemas kini',
            'booking' => $booking,
        ]);
    }

    /**
     * Get color based on booking status
     */
    private function getEventColor($status)
    {
        return match($status) {
            'pending' => '#f59e0b',    // yellow
            'approved' => '#10b981',   // green
            'rejected' => '#ef4444',   // red
            'completed' => '#3b82f6',  // blue
            'cancelled' => '#6b7280',  // gray
            default => '#9ca3af',
        };
    }

    /**
     * Display the vehicle/document management page.
     */
    public function vehicle()
    {
        return view('admin.vehicle');
    }

    /**
     * Display the notifications page.
     */
    public function notification()
    {
        return view('admin.notification');
    }

    /**
     * Display the admin profile page.
     */
    public function profile()
    {
        return view('admin.profile');
    }

    /**
     * Display the logout confirmation page.
     */
    public function logoutPage()
    {
        return view('admin.logout');
    }

    /**
     * Display the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Display the admin registration form.
     */
    public function showRegistrationForm()
    {
        return view('admin.auth.register');
    }

    /**
     * Display the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('admin.auth.forgot');
    }
}
