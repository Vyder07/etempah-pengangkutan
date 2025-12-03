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
use App\Models\User;
use App\Notifications\VerifyEmailNotification;

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
            $user = Auth::user();

            // Check if account is verified (for both admin and staff)
            if (!$user->is_verified) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Sila sahkan e-mel anda terlebih dahulu. Semak peti masuk anda.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();

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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|in:admin,staff'
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');
        $role = $request->input('role') ?? 'staff';

        // Generate verification token
        $verificationToken = Str::random(60);

        // Create user with Eloquent to use notifications
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
            'verification_token' => $verificationToken,
            'is_verified' => false,
            'email_verified_at' => null,
        ]);

        // Send verification email with appropriate route based on role
        $verificationUrl = route($role === 'admin' ? 'admin.verify.email' : 'staff.verify.email', ['token' => $verificationToken]);
        $user->notify(new VerifyEmailNotification($verificationUrl));

        return redirect()->route($role === 'admin' ? 'admin.auth.login' : 'staff.auth.login')
            ->with('success', 'Akaun berjaya didaftarkan! Sila semak e-mel anda untuk mengesahkan akaun.');
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
            // Determine redirect based on user role
            $user = User::where('email', $request->email)->first();
            $loginRoute = ($user && $user->role === 'staff') ? 'staff.auth.login' : 'admin.auth.login';

            return redirect()->route($loginRoute)
                ->with('success', 'Kata laluan berjaya ditetapkan semula. Sila log masuk.');
        }

        return back()->withErrors(['email' => __($status)]);
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

    public function showStaffResetForm(Request $request, $token)
    {
        return view('staff.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Show the default password reset form (redirects based on email)
     */
    public function showResetForm(Request $request, $token)
    {
        // Default to admin reset form, but can be customized
        return view('admin.auth.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function showLoginOptions()
    {
        return view('login.index');
    }

    /**
     * VERIFY EMAIL
     */
    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            // Try to detect role from URL or default to admin
            $loginRoute = request()->is('staff/*') ? 'staff.auth.login' : 'admin.auth.login';
            return redirect()->route($loginRoute)
                ->withErrors(['email' => 'Token pengesahan tidak sah atau telah tamat tempoh.']);
        }

        // Update user as verified
        $user->is_verified = true;
        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        // Redirect to appropriate login based on user role
        $loginRoute = $user->role === 'staff' ? 'staff.auth.login' : 'admin.auth.login';
        return redirect()->route($loginRoute)
            ->with('success', 'E-mel anda berjaya disahkan! Sila log masuk.');
    }

    /**
     * RESEND VERIFICATION EMAIL
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->is_verified) {
            return back()->with('info', 'Akaun ini sudah disahkan.');
        }

        // Generate new token
        $verificationToken = Str::random(60);
        $user->verification_token = $verificationToken;
        $user->save();

        // Send new verification email with appropriate route based on role
        $verificationUrl = route($user->role === 'staff' ? 'staff.verify.email' : 'admin.verify.email', ['token' => $verificationToken]);
        $user->notify(new VerifyEmailNotification($verificationUrl));

        return back()->with('success', 'E-mel pengesahan telah dihantar semula. Sila semak peti masuk anda.');
    }
}
