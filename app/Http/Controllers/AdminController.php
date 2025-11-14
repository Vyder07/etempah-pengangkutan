<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EventBanner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public function booking()
    {
        return view('admin.booking');
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
