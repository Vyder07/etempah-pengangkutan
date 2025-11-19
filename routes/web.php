<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StaffController;

Route::middleware(['guest'])->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginOptions'])->name('login');
    Route::get('/activate/{token}', [AuthController::class, 'activate']);
    Route::post('/password/email', [AuthController::class, 'sendResetLink']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);

    Route::controller(AuthController::class)
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/login', 'showStaffLoginForm')->name('auth.login');
        Route::post('/login', 'login')->name('login.submit');
        Route::get('/register', 'showStaffRegistrationForm')->name('auth.register');
        Route::post('/register', 'register')->name('register.submit');
        Route::get('/forgot-password', 'showStaffForgotPasswordForm')->name('forgot');
        Route::post('/forgot-password', 'sendResetLink')->name('forgot.submit');
    });

    Route::controller(AdminController::class)
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/login', 'showLoginForm')->name('auth.login');
            Route::get('/register', 'showRegistrationForm')->name('auth.register');
            Route::get('/forgot-password', 'showForgotPasswordForm')->name('auth.forgot');
        });

    Route::controller(AuthController::class)
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::post('/login', 'login')->name('auth.login.submit');
            Route::post('/register', 'register')->name('auth.register.submit');
            Route::post('/forgot-password', 'sendResetLink')->name('auth.forgot.submit');
        });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function() {
        return view('welcome');
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin authenticated routes
    Route::controller(AdminController::class)
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/booking', 'booking')->name('booking');
            Route::get('/vehicle', 'vehicle')->name('vehicle');
            Route::get('/notification', 'notification')->name('notification');
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'updateProfile')->name('profile.update');
            Route::post('/profile/photo', 'uploadProfilePhoto')->name('profile.photo');
            Route::get('/logout', 'logoutPage')->name('logout.page');

            // Event Banner routes
            Route::post('/banners', 'storeBanner')->name('banners.store');
            Route::put('/banners/{id}', 'updateBanner')->name('banners.update');
            Route::delete('/banners/{id}', 'deleteBanner')->name('banners.delete');
            Route::post('/banners/reorder', 'reorderBanners')->name('banners.reorder');

            // Booking routes
            Route::get('/bookings/data', 'getBookings')->name('bookings.data');
            Route::get('/bookings/{id}', 'getBooking')->name('bookings.show');
            Route::put('/bookings/{id}/status', 'updateBookingStatus')->name('bookings.updateStatus');

            // Document/Attachment routes
            Route::get('/documents/data', 'getBookingAttachments')->name('documents.data');
            Route::delete('/documents/{id}', 'deleteBookingAttachment')->name('documents.delete');

            // Notification routes
            Route::put('/notifications/{id}/status', 'updateNotificationStatus')->name('notifications.updateStatus');
        });

    // Staff authenticated routes
    Route::controller(StaffController::class)
        ->prefix('staff')
        ->name('staff.')
        ->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/booking', 'booking')->name('booking');
            Route::get('/notification', 'notification')->name('notification');
            Route::get('/history', 'history')->name('history');
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'updateProfile')->name('profile.update');
            Route::post('/profile/photo', 'uploadProfilePhoto')->name('profile.photo');
            Route::post('/logout', 'logout')->name('logout');
        });
});

// ===========================
//  AUTH API ROUTES
// ===========================

// Route::post('/logout', [AuthController::class, 'logout']);
// Route::post('/register', [AuthController::class, 'register']);

// NOTE: API-prefixed routes are defined in `routes/api.php` (use those to avoid conflicts).

// Activation and password reset

// ===========================
//  BASIC TEST ROUTE
// ===========================
// Route::middleware(['auth'])->get('/', function () {
//     return response()->json(['message' => 'Backend Laravel siap untuk frontend']);
// });

// ===========================
//  FRONTEND ROUTES
// ===========================
// Route::get('/login', function () {
//     return redirect('/login/index.html');
// });

// Route::get('/register', function () {
//     return redirect('/register/index.html');
// });

// Route::get('/dashboard', function () {
//     return redirect('/dashboard.html');
// });

// ===========================
//  STATIC FRONTEND SERVE (for Apache setups)
// ===========================
$serveFromBase = public_path();

Route::get('/assets/{path}', function ($path) use ($serveFromBase) {
    $file = $serveFromBase . '/assets/' . $path;
    if (file_exists($file)) {
        return response()->file($file);
    }
    abort(404);
})->where('path', '.*');

// ===========================
//  WILDCARD ROUTE (MUST BE LAST)
// ===========================
Route::get('/{path}', function ($path = '') use ($serveFromBase) {
    $file = $serveFromBase . '/' . $path;

    if (file_exists($file) && !is_dir($file)) {
        return response()->file($file);
    }

    return response()->json(['error' => 'Not Found'], 404);
})->where('path', '.*');
