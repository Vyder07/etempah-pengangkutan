<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        return view('admin.dashboard');
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
