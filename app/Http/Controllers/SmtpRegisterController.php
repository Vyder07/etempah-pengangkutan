<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SmtpRegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'name' => 'nullable|string',
        ]);

        // Use provided name or derive from email
        $name = $request->input('name') ?: explode('@', $request->email)[0];

        // Create user immediately with hashed password and mark email verified for convenience
        $user = User::create([
            'name' => $name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => now(),
        ]);

        // Optionally send a welcome email (kept simple)
        try {
            Mail::raw("Selamat datang, {$name}! Akaun anda telah didaftarkan.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Selamat Datang');
            });
        } catch (\Throwable $e) {
            // Mail failures shouldn't prevent successful registration
        }

        return response()->json([
            'success' => true,
            'message' => 'Akaun berjaya didaftarkan.'
        ]);
    }
    public function activate($token)
{
    $user = User::where('activation_token', $token)->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'Token tidak sah.'
        ]);
    }

    // Set password default (atau ikut flow awak)
    $user->password = bcrypt('password_default'); 
    $user->activation_token = null;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Akaun telah diaktifkan.'
    ]);
}

}
