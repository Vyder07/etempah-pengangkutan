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

// LOGIN
Route::post('/login', [AuthController::class, 'login']);

// REGISTER
Route::post('/register', [AuthController::class, 'register']);

// ACTIVATE
Route::get('/activate/{token}', [AuthController::class, 'activate']);

// PASSWORD RESET
Route::post('/password/email', [AuthController::class, 'sendResetLink']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
