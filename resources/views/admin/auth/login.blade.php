@extends('admin.auth.layout')

@section('title', 'Log Masuk Admin')

@section('content')
<div class="login-box">
    <h2>Log Masuk Admin</h2>

    @if ($errors->any())
        <div id="login-msg" style="color: #ff8080; margin-bottom: 15px;">
            {{ $errors->first() }}
        </div>
    @endif

    @if (session('success'))
        <div style="color: #4ade80; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div id="login-msg" style="color: #ff8080; margin-bottom: 15px;">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.auth.login.submit') }}">
        @csrf
        <input type="email" name="email" id="email" placeholder="Email Address" value="{{ old('email') }}" required />
        <input type="password" name="password" id="password" placeholder="Password" required />

        <div class="options">
            <label>
                <input type="checkbox" name="remember" /> Ingat Saya
            </label>
            <a href="{{ route('admin.auth.forgot') }}">Terlupa Kata Laluan?</a>
        </div>

        <button type="submit">Log Masuk</button>

        <p class="signup-text">
            Belum mempunyai akaun? <a href="{{ route('admin.auth.register') }}">Daftar.</a>
        </p>
    </form>
</div>
@endsection