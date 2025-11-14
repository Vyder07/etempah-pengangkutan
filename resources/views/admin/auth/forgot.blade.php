@extends('admin.auth.layout')

@section('title', 'Reset Password')

@section('content')
<div class="login-box">
    <h2>Reset Password</h2>

    @if ($errors->any())
        <p id="reset-message" style="margin-top:10px; color: #f87171;">
            {{ $errors->first() }}
        </p>
    @endif

    @if (session('success'))
        <p id="reset-message" style="margin-top:10px; color: #4ade80;">
            {{ session('success') }}
        </p>
    @endif

    <form method="POST" action="{{ route('admin.auth.forgot.submit') }}">
        @csrf
        <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email') }}" required />
        <button type="submit">EMAIL ME</button>
    </form>

    <p class="signup-text" style="margin-top: 15px;">
        Ingat kata laluan? <a href="{{ route('admin.auth.login') }}">Log Masuk.</a>
    </p>
</div>
@endsection
