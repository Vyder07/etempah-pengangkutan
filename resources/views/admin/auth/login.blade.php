@extends('admin.auth.layout')

@section('title', 'Log Masuk Admin')

@section('content')
<div class="login-box">
    <h2>Log Masuk Admin</h2>

    @if ($errors->any())
        <div id="login-msg" style="color: #ff8080; margin-bottom: 15px;">
            {{ $errors->first() }}
            @if(str_contains($errors->first(), 'sahkan e-mel'))
                <div style="margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('resendForm').style.display='block'"
                            style="background: none; border: none; color: #4ade80; cursor: pointer; text-decoration: underline; padding: 0;">
                        Hantar semula e-mel pengesahan
                    </button>
                </div>
                <form id="resendForm" method="POST" action="{{ route('admin.resend.verification') }}" style="display: none; margin-top: 10px;">
                    @csrf
                    <input type="email" name="email" placeholder="Masukkan e-mel anda" required
                           style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;" />
                    <button type="submit" style="width: 100%; padding: 10px; background: #4ade80; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Hantar Semula
                    </button>
                </form>
            @endif
        </div>
    @endif

    @if (session('success'))
        <div style="color: #4ade80; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('info'))
        <div style="color: #60a5fa; margin-bottom: 15px;">
            {{ session('info') }}
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
