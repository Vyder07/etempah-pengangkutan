<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Reset Password</title>
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
      background: rgba(0, 0, 0, 0.389);
      backdrop-filter: blur(1px);
      padding: 30px;
      width: 320px;
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
      margin-bottom: 24px;
      padding: 12px 15px;
      border: 1px solid #0bf3ff;
      border-radius: 8px;
      background-color: #ffffff;
      outline: none;
      box-sizing: border-box;
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

    .error-message {
      color: #f87171;
      margin-bottom: 15px;
      font-size: 0.9em;
    }

    .success-message {
      color: #4ade80;
      margin-bottom: 15px;
      font-size: 0.9em;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Reset Password</h2>

    @if ($errors->any())
      <p class="error-message">{{ $errors->first() }}</p>
    @endif

    @if (session('success'))
      <p class="success-message">{{ session('success') }}</p>
    @endif

    <form method="POST" action="{{ route('staff.reset.submit') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="email" name="email" placeholder="Email Address" value="{{ $email ?? old('email') }}" required />
      <input type="password" name="password" placeholder="New Password" required />
      <input type="password" name="password_confirmation" placeholder="Confirm Password" required />
      <button type="submit">RESET PASSWORD</button>
    </form>

    <p class="signup-text">
      Ingat kata laluan? <a href="{{ route('staff.auth.login') }}">Log Masuk.</a>
    </p>
  </div>
</body>
</html>
