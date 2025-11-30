<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes untuk frontend fetch JSON dari formad.html atau register.html
|
*/

// Note: Login and Register routes moved to web.php for session support
// Use /staff/login or /admin/login instead of /api/login

// ACTIVATE
Route::get('/activate/{token}', [AuthController::class, 'activate']);

// PASSWORD RESET
Route::post('/password/email', [AuthController::class, 'sendResetLink']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
