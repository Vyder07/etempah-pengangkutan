<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use App\Mail\ActivationEmail;

class AuthController extends Controller
{
    /**
     * LOGIN
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Use Laravel's Auth::attempt to authenticate the user
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Ensure we read role from the users table (may be null)
            $role = null;
            if (isset($user->role)) {
                $role = $user->role;
            }

            // Choose redirect target based on role
            if ($role === 'admin') {
                $redirect = url('/admin/dashboard');
            } else {
                $redirect = url('/home');
            }

            return response()->json([
                'success' => true,
                'role' => $role,
                'redirect' => $redirect,
                'message' => 'Log masuk berjaya.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password.'
        ], 401);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        // Untuk sekarang, cuma return success message
        return response()->json([
            'success' => true,
            'message' => 'Logout berjaya.'
        ]);
    }

    /**
     * REGISTER (DAFTAR AKAUN BARU)
     */
    public function register(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $role = $request->input('role') ?? 'staff';
        // Basic validation
        if (empty($email) || empty($password)) {
            return response()->json(['success' => false, 'message' => 'Email dan kata laluan diperlukan.'], 422);
        }

        // Semak email dah digunakan atau belum (use `email` column)
        $exists = DB::table('users')->where('email', $email)->first();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Email sudah digunakan.'
            ], 409);
        }

        // Simpan user baru. The default users table in this project has
        // `name`, `email`, `password`, `email_verified_at`, `timestamps`.
        // We'll set a sensible `name` and mark email as verified so user can log in.
        DB::table('users')->insert([
            'name' => explode('@', $email)[0],
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akaun berjaya didaftarkan.'
        ]);
    }

    /**
     * AKTIFKAN AKAUN (BILA TEKAN LINK DARI EMAIL)
     */
    public function activate($token)
    {
        // If the users table doesn't have activation columns, return a helpful message
        if (!Schema::hasColumn('users', 'activation_token')) {
            return response()->json([
                'success' => false,
                'message' => 'Activation flow is not enabled on this installation.'
            ], 501);
        }

        $user = DB::table('users')->where('activation_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak sah atau sudah digunakan.'
            ], 404);
        }

        // Aktifkan akaun
        DB::table('users')->where('id', $user->id)->update([
            'is_active' => 1,
            'activation_token' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akaun berjaya diaktifkan. Anda kini boleh log masuk.'
        ]);
    }

    /**
     * Send password reset link to email
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['success' => true, 'message' => __($status)]);
        }

        return response()->json(['success' => false, 'message' => __($status)], 500);
    }

    /**
     * Reset password using token
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['success' => true, 'message' => __($status)]);
        }

        return response()->json(['success' => false, 'message' => __($status)], 500);
    }
}
