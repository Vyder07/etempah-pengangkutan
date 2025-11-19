<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daftar Akaun</title>
  <style>
    /* Gradient background */
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      background-image: url('{{ asset('IMG/background.png') }}');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Container box */
    .login-box {
      background: rgba(0, 0, 0, 0.389); /* gelap kabur */
      backdrop-filter: blur(1px);
      padding: 30px; /* kurang sikit */
      width: 320px;  /* tambahkan ni untuk kawal lebar */
      border-radius: 20px;
      text-align: center;
    }

    .login-box h2 {
      margin-bottom: 20px;
      color: white;
    }

    /* Input field */
    .login-box input[type="text"],
    .login-box input[type="password"],
    .login-box input[type="email"] {
      width: 100%;
      margin-bottom: 24px; /* Tambah spacing */
      padding: 12px 15px;
      border: 1px solid #0bf3ff;
      border-radius: 8px;
      background-color: #ffffff;
      outline: none;
      box-sizing: border-box; /* <-- Ini penting */
    }

    /* Remember & forgot password */
    .options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.9em;
      margin-bottom: 20px;
      color: #ffffff;
    }

    .options a {
      color: #10dbff;
      text-decoration: none;
    }

    /* Login button */
    .login-box button {
      width: 100%;
      padding: 12px;
      background-color: #4dbeff;
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    .login-box button:hover {
      background-color: #1a45df;
    }

    /* Create account */
    .signup-text {
      margin-top: 15px;
      font-size: 0.9em;
      color : white;
    }

    .signup-text a {
      color: #07a4ff;
      text-decoration: none;
    }

    /* Error message styling */
    .error-message {
      background-color: rgba(239, 68, 68, 0.9);
      color: white;
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 0.9em;
      text-align: left;
      border-left: 4px solid #dc2626;
    }

    .error-message ul {
      margin: 0;
      padding-left: 20px;
    }

    .error-message li {
      margin: 5px 0;
    }

    .success-message {
      background-color: rgba(16, 185, 129, 0.9);
      color: white;
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 15px;
      font-size: 0.9em;
      text-align: center;
      border-left: 4px solid #059669;
    }

    /* Input error state */
    .login-box input.error {
      border-color: #ef4444;
      background-color: #fee;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h2>DAFTAR</h2>

      @if($errors->any())
        <div class="error-message">
          <strong>Ralat!</strong>
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if(session('success'))
        <div class="success-message">
          {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div class="error-message">
          {{ session('error') }}
        </div>
      @endif

      <form id="login-form" action="{{ route('staff.register.submit') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Nama Penuh" value="{{ old('name') }}" required class="{{ $errors->has('name') ? 'error' : '' }}" />
        <input type="email" name="email" placeholder="Alamat Email" value="{{ old('email') }}" required class="{{ $errors->has('email') ? 'error' : '' }}" />
        <input type="password" name="password" placeholder="Kata Laluan" required class="{{ $errors->has('password') ? 'error' : '' }}" />
        <input type="password" name="password_confirmation" placeholder="Sahkan Kata Laluan" required class="{{ $errors->has('password') ? 'error' : '' }}" />

        <div class="options"></div>

        <button type="submit">DAFTAR AKAUN</button>
      </form>

      <div class="signup-text">
        Sudah mempunyai akaun? <a href="{{ route('staff.auth.login') }}">Log Masuk</a>
      </div>
    </div>
  </div>
</body>
</html>
