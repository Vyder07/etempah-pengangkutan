@extends('admin.auth.layout')

@section('title', 'Pendaftaran Akaun')

@section('content')
<div class="login-box">
    <h2>DAFTAR AKAUN</h2>

    @if ($errors->any())
        <div id="register-msg" style="color: #ff8080; margin-bottom: 15px;">
            @foreach ($errors->all() as $error)
                {{ $error }}<br>
            @endforeach
        </div>
    @endif

    @if (session('success'))
        <div style="color: #4ade80; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.auth.register.submit') }}">
        @csrf
        <input type="email" name="email" id="email" placeholder="Alamat Emel" value="{{ old('email') }}" required />
        <input type="password" name="password" id="password" placeholder="Kata Laluan" required />
        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Sahkan Kata Laluan" required />
        <input type="hidden" name="role" value="admin" />
        <button type="submit">Daftar Sekarang</button>
    </form>

    <p class="signup-text" style="margin-top: 15px;">
        Sudah mempunyai akaun? <a href="{{ route('admin.auth.login') }}">Log Masuk.</a>
    </p>
</div>
@endsection
