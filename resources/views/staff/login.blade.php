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

      <form id="login-form" action="{{ route('staff.auth.login') }}" method="POST">
        @csrf
        <input type="email" name="email" placeholder="Email Address" required />
        <input type="password" name="password" placeholder="Password" required />

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
