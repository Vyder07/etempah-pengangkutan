<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Log Masuk</title>
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
      backdrop-filter: blur(3px);
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

    .topbar {
      position: fixed;
      left: 110px;
      right: 0;
      top: 0;
      height: 64px;
      display: flex;
      align-items: center;
      padding: 0 18px;
      background: transparent;
      backdrop-filter: none;
      z-index: 999;
    }

    .search-wrap {
      max-width: 1100px;
      width: 100%;
      display: flex;
      gap: 8px;
      background: #ffffff;
      padding: 8px;
      border-radius: 8px;
    }

    .search-input {
      flex: 1;
      padding: 8px 12px;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      font-size: 14px;
      background: #fff;
    }

    .search-btn {
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      background: #0b63ff;
      color: #fff;
      cursor: pointer;
    }

    .kotak, .content, main {
      margin-top: 84px;
    }

    @media(max-width: 800px) {
      .topbar {
        left: 0;
      }
      .kotak, .content, main {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h2>Log Masuk Staff</h2>

      @if($errors->any())
        <div class="error-message">
          <strong>Ralat!</strong>
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          @if($errors->has('email') && str_contains($errors->first('email'), 'sahkan e-mel'))
            <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.3);">
              <button type="button" onclick="document.getElementById('resendForm').style.display='block'"
                      style="background: none; border: none; color: #4ade80; cursor: pointer; text-decoration: underline; padding: 0; font-size: 14px;">
                Hantar semula e-mel pengesahan
              </button>
            </div>
            <form id="resendForm" method="POST" action="{{ route('staff.resend.verification') }}" style="display: none; margin-top: 15px;">
              @csrf
              <input type="email" name="email" placeholder="Masukkan e-mel anda" required
                     style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #0bf3ff; border-radius: 8px; box-sizing: border-box;" />
              <button type="submit" style="width: 100%; padding: 10px; background: #4ade80; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">
                Hantar Semula
              </button>
            </form>
          @endif
        </div>
      @endif

      @if(session('success'))
        <div class="success-message">
          {{ session('success') }}
        </div>
      @endif

      @if(session('info'))
        <div style="background-color: rgba(96, 165, 250, 0.9); color: white; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9em;">
          {{ session('info') }}
        </div>
      @endif

      @if(session('error'))
        <div class="error-message">
          {{ session('error') }}
        </div>
      @endif

      <form id="login-form" action="{{ route('staff.auth.login') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}" required class="{{ $errors->has('email') ? 'error' : '' }}" />
        <input type="password" name="password" placeholder="Password" required class="{{ $errors->has('password') ? 'error' : '' }}" />

        <div class="options">
          <label>
            <input type="checkbox" name="remember" />
            Ingat Saya
          </label>
          <a href="{{ route('staff.forgot') }}">Terlupa Kata Laluan</a>
        </div>

        <button type="submit">LOG MASUK</button>

        <p class="signup-text">
          Belum mempunyai akaun? <a href="{{ route('staff.auth.register') }}">Daftar. </a>
        </p>
      </form>
    </div>
  </div>

  <script>
    function globalSearch() {
      const q = document.getElementById('globalSearch').value.trim();
      if (!q) return;
      const found = document.querySelectorAll('body *:not(script):not(style)');
      for (const el of found) {
        if (el.textContent && el.textContent.toLowerCase().includes(q.toLowerCase())) {
          el.scrollIntoView({behavior: 'smooth', block: 'center'});
          el.style.outline = '3px solid #ffea00';
          setTimeout(() => el.style.outline = '', 3000);
          return;
        }
      }
      alert('No results on this page.');
    }
  </script>
</body>
</html>
