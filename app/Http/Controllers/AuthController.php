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
        $remember = $request->has('remember');

        // Use Laravel's Auth::attempt to authenticate the user
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Ensure we read role from the users table (may be null)
            $role = $user->role ?? null;

            // Choose redirect target based on role
            if ($role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($role === 'staff') {
                return redirect()->intended(route('staff.dashboard'));
            } else {
                return redirect()->intended('/home');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau kata laluan tidak sah.',
        ])->withInput($request->only('email'));
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        // logout the user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * REGISTER (DAFTAR AKAUN BARU)
     */
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|in:admin,staff'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        $role = $request->input('role') ?? 'staff';

        // Simpan user baru
        DB::table('users')->insert([
            'name' => explode('@', $email)[0],
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route($role === 'admin' ? 'admin.auth.login' : 'staff.auth.login')
            ->with('success', 'Akaun berjaya didaftarkan. Sila log masuk.');
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
            return back()->with('success', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
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

    public function showStaffLoginForm()
    {
        return view('staff.login');
    }

    public function showStaffRegistrationForm()
    {
        return view('staff.register');
    }

    public function showStaffForgotPasswordForm()
    {
        return view('staff.forgot');
    }

    public function showLoginOptions()
    {
        return view('login.index');
    }
}
