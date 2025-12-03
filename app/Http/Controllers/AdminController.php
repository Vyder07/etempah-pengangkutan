<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventBanner;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $banners = EventBanner::getActiveBanners();

        // Booking statistics
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $approvedBookings = Booking::where('status', 'approved')->count();
        $todayBookings = Booking::whereDate('start_date', Carbon::today())->count();

        // Recent bookings (last 5)
        $recentBookings = Booking::with('user')
            ->latest()
            ->take(3)
            ->get();

        // Upcoming bookings
        $upcomingBookings = Booking::with('user')
            ->where('start_date', '>=', Carbon::now())
            ->where('status', 'approved')
            ->orderBy('start_date')
            ->take(3)
            ->get();

        return view('admin.dashboard', compact(
            'banners',
            'totalBookings',
            'pendingBookings',
            'approvedBookings',
            'todayBookings',
            'recentBookings',
            'upcomingBookings'
        ));
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

        // Broadcast notification to staff via WebSocket
        broadcast(new \App\Events\BookingUpdated($booking))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Status tempahan berjaya dikemas kini',
            'booking' => $booking,
        ]);
    }

    /**
     * Download booking as PDF
     */
    public function downloadBookingPdf($id)
    {
        $booking = Booking::with('user')->findOrFail($id);

        // Generate PDF
        $pdf = Pdf::loadView('admin.booking-pdf', compact('booking'));

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Download with filename
        $filename = 'Tempahan_' . $booking->id . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
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
        $bookings = Booking::with(['user', 'media'])
            ->latest()
            ->paginate(15);

        return view('admin.vehicle', compact('bookings'));
    }

    /**
     * Display the notifications page.
     */
    public function notification()
    {
        $bookings = Booking::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.notification', compact('bookings'));
    }

    /**
     * Update booking status from notification page
     */
    public function updateNotificationStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $booking = Booking::findOrFail($id);
        $booking->status = $request->status;
        if ($request->notes) {
            $booking->notes = $request->notes;
        }
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Status tempahan berjaya dikemaskini',
            'status' => $booking->status,
            'status_label' => $booking->status_label,
            'status_color' => $booking->status_color
        ]);
    }

    /**
     * Delete a booking from notification page
     */
    public function deleteNotification($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permohonan berjaya dipadam'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memadam permohonan'
            ], 500);
        }
    }

    /**
     * Display the admin profile page.
     */
    public function profile()
    {
        return view('admin.profile');
    }

    /**
     * Update the admin profile.
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        $user = auth()->user();
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

        return redirect()->route('admin.profile')->with('success', 'Profil berjaya dikemaskini');
    }

    /**
     * Upload admin profile photo
     */
    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

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

    /**
     * Display the reset password form.
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('admin.auth.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

        /**
     * Get booking attachments as JSON for the vehicle/documents page
     */
    public function getBookingAttachments()
    {
        $bookings = Booking::with(['user', 'media'])
            ->whereHas('media')
            ->latest()
            ->get();

        $documents = [];
        foreach ($bookings as $booking) {
            foreach ($booking->getMedia('attachments') as $media) {
                $documents[] = [
                    'id' => $media->id,
                    'booking_id' => $booking->id,
                    'name' => $media->file_name,
                    'date' => $media->created_at->format('Y-m-d'),
                    'size' => $this->formatBytes($media->size),
                    'summary' => "Tempahan: {$booking->vehicle_name} - {$booking->destination}",
                    'staff' => $booking->user->name,
                    'url' => $media->getUrl(),
                    'mime_type' => $media->mime_type,
                ];
            }
        }

        return response()->json($documents);
    }

    /**
     * Get all bookings as JSON for the vehicle page table
     */
    public function getVehicleBookings()
    {
        $bookings = Booking::with('user')
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'vehicle_type' => $booking->vehicle_type,
                    'vehicle_name' => $booking->vehicle_name,
                    'vehicle_plate' => $booking->vehicle_plate,
                    'user_name' => $booking->user->name,
                    'destination' => $booking->destination,
                    'purpose' => $booking->purpose,
                    'start_date' => $booking->start_date->format('d/m/Y'),
                    'end_date' => $booking->end_date->format('d/m/Y'),
                    'status' => $booking->status,
                    'status_label' => $booking->status_label,
                    'created_at' => $booking->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json($bookings);
    }

    /**
     * Delete booking attachment
     */
    public function deleteBookingAttachment($id)
    {
        try {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($id);
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Fail berjaya dipadam'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memadam fail'
            ], 500);
        }
    }

    /**
     * Format bytes to human readable size
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1024 ** $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Global search across modules
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'bookings' => [],
                'users' => []
            ]);
        }

        // Search bookings
        $bookings = Booking::with('user')
            ->where(function($q) use ($query) {
                $q->where('vehicle_name', 'LIKE', "%{$query}%")
                  ->orWhere('vehicle_plate', 'LIKE', "%{$query}%")
                  ->orWhere('vehicle_type', 'LIKE', "%{$query}%")
                  ->orWhere('destination', 'LIKE', "%{$query}%")
                  ->orWhere('purpose', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function($q) use ($query) {
                      $q->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'vehicle_name' => $booking->vehicle_name,
                    'vehicle_plate' => $booking->vehicle_plate,
                    'vehicle_type' => $booking->vehicle_type,
                    'user_name' => $booking->user->name,
                    'destination' => $booking->destination,
                    'purpose' => $booking->purpose,
                    'status' => $booking->status,
                    'status_label' => $booking->status_label,
                ];
            });

        // Search users (staff only, not admins for privacy)
        $users = User::where('role', 'staff')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ];
            });

        return response()->json([
            'bookings' => $bookings,
            'users' => $users
        ]);
    }
}
