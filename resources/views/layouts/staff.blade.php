<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title', 'Staff Portal')</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

  @stack('styles')

  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }

    body {
      min-height: 100vh;
      width: 100%;
      background-image: url('{{ asset('IMG/Car1.jpg') }}');
      background-position: center;
      background-size: cover;
      background-repeat: no-repeat;
    }

    /* Sidebar */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      height: 100%;
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(17px);
      border-right: 1px solid rgba(255,255,255,0.7);
      display: flex;
      flex-direction: column;
      padding: 20px 0;
      z-index: 1000;
    }

    .logo {
      display: flex;
      align-items: center;
      padding: 0 20px;
      color: #000;
    }

    .logo img {
      width: 43px;
      border-radius: 50%;
    }

    .logo h2 {
      font-size: 1.15rem;
      font-weight: 600;
      margin-left: 15px;
    }

    .links {
      list-style: none;
      padding: 0 8px;
    }

    .links li {
      display: flex;
      align-items: center;
      border-radius: 4px;
      padding: 10px;
      transition: background 0.3s;
    }

    .links li:hover {
      background: rgba(0,0,0,0.05);
      cursor: pointer;
    }

    .links li span {
      padding-right: 10px;
      font-size: 24px;
      color: #000;
    }

    .links li a {
      text-decoration: none;
      color: #000;
      font-weight: 500;
      white-space: nowrap;
    }

    .links li.active {
      background: #0a4c9a;
      border-left: 4px solid #fff;
    }

    .links li.active a,
    .links li.active span {
      color: #fff;
      font-weight: 600;
    }

    .bottom-links {
      margin-top: auto;
    }

    /* Topbar */
    .topbar {
      position: fixed;
      left: 260px;
      right: 0;
      top: 0;
      height: 64px;
      display: flex;
      align-items: center;
      padding: 0 18px;
      background: transparent;
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
      border: none;
    }

    /* Main content */
    .content {
      margin-left: 260px;
      margin-top: 84px;
      padding: 20px;
    }

    /* Logout modal */
    .modal {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.5);
      justify-content: center;
      align-items: center;
      z-index: 10000;
    }

    .modal[aria-hidden="false"] {
      display: flex;
    }

    .modal-backdrop {
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.45);
    }

    .modal-panel {
      position: relative;
      background: #fff;
      border-radius: 10px;
      padding: 18px 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.15);
      max-width: 360px;
      width: 90%;
      z-index: 1;
      text-align: center;
    }

    .modal-panel h3 {
      margin: 0 0 8px;
      font-size: 1.05rem;
    }

    .modal-panel p {
      margin: 0 0 12px;
      color: #444;
    }

    .modal-actions {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin-top: 8px;
    }

    .btn {
      padding: 8px 14px;
      border-radius: 8px;
      border: 1px solid #ccc;
      background: #f5f5f5;
      cursor: pointer;
    }

    .btn.primary {
      background: #dc2626;
      color: #fff;
      border-color: #dc2626;
    }

    @media(max-width: 800px) {
      .topbar {
        left: 0;
      }
      .content {
        margin-left: 0;
      }
      .sidebar {
        transform: translateX(-100%);
      }
      .sidebar.mobile-open {
        transform: translateX(0);
      }
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo">
      <img src="{{ asset('IMG/PROTON.png') }}" alt="logo">
      <h2>Staf</h2>
    </div>
    <ul class="links main-links">
      <li class="{{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        <span class="material-symbols-outlined">dashboard</span>
        <a href="{{ route('staff.dashboard') }}">Laman Utama</a>
      </li>
      <li class="{{ request()->routeIs('staff.booking') ? 'active' : '' }}">
        <span class="material-symbols-outlined">car_rental</span>
        <a href="{{ route('staff.booking') }}">Tempahan</a>
      </li>
      <li class="{{ request()->routeIs('staff.notification') ? 'active' : '' }}">
        <span class="material-symbols-outlined">notifications</span>
        <a href="{{ route('staff.notification') }}">Notifikasi</a>
      </li>
      <li class="{{ request()->routeIs('staff.history') ? 'active' : '' }}">
        <span class="material-symbols-outlined">history</span>
        <a href="{{ route('staff.history') }}">Sejarah Tempahan</a>
      </li>
    </ul>
    <ul class="links bottom-links">
      <li class="{{ request()->routeIs('staff.profile') ? 'active' : '' }}">
        <span class="material-symbols-outlined">person</span>
        <a href="{{ route('staff.profile') }}">Profil</a>
      </li>
      <li class="logout-link">
        <span class="material-symbols-outlined">logout</span>
        <a href="#">Log Keluar</a>
      </li>
    </ul>
  </aside>

  <!-- Topbar -->
  <header class="topbar">
    <div class="search-wrap">
      <input id="globalSearch" class="search-input" type="search" placeholder="@yield('search-placeholder', 'Search site...')" aria-label="Search">
      <button class="search-btn" type="button" onclick="globalSearch()">🔍</button>
    </div>
  </header>

  <!-- Main Content -->
  <main class="content">
    @yield('content')
  </main>

  <!-- Logout Modal -->
  <div id="logoutModal" class="modal" aria-hidden="true">
    <div class="modal-backdrop" tabindex="-1"></div>
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="logoutTitle">
      <h3 id="logoutTitle">Sahkan Log Keluar</h3>
      <p>Adakah anda pasti mahu log keluar?</p>
      <div class="modal-actions">
        <button id="confirmLogout" class="btn primary">Ya, Log Keluar</button>
        <button id="cancelLogout" class="btn">Tidak</button>
      </div>
    </div>
  </div>

  <script>
    // Global search function
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

    // Logout modal script
    (function(){
      const logoutLink = document.querySelector('.logout-link a');
      if (!logoutLink) return;

      const modal = document.getElementById('logoutModal');
      const confirmBtn = document.getElementById('confirmLogout');
      const cancelBtn = document.getElementById('cancelLogout');
      const backdrop = modal.querySelector('.modal-backdrop');

      function openModal(href) {
        modal.dataset.href = href || '';
        modal.setAttribute('aria-hidden', 'false');
        confirmBtn.focus();
      }

      function closeModal() {
        modal.setAttribute('aria-hidden', 'true');
        delete modal.dataset.href;
        logoutLink.focus();
      }

      logoutLink.addEventListener('click', function(e) {
        e.preventDefault();
        openModal(this.getAttribute('href'));
      });

      confirmBtn.addEventListener('click', function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('staff.logout') }}';
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
      });

      cancelBtn.addEventListener('click', closeModal);
      backdrop.addEventListener('click', closeModal);
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
      });
    })();
  </script>

  @stack('scripts')
</body>
</html>
