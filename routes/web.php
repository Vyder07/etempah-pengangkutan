<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ===========================
//  AUTH API ROUTES
// ===========================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);

// NOTE: API-prefixed routes are defined in `routes/api.php` (use those to avoid conflicts).

// Activation and password reset
Route::get('/activate/{token}', [AuthController::class, 'activate']);
Route::post('/password/email', [AuthController::class, 'sendResetLink']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

// ===========================
//  BASIC TEST ROUTE
// ===========================
Route::get('/', function () {
    return response()->json(['message' => 'Backend Laravel siap untuk frontend']);
});

// ===========================
//  FRONTEND ROUTES
// ===========================
Route::get('/login', function () {
    return redirect('/login/index.html');
});

Route::get('/register', function () {
    return redirect('/register/index.html');
});

Route::get('/dashboard', function () {
    return redirect('/dashboard.html');
});

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
