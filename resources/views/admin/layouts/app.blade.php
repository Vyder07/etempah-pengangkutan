<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Admin CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    @stack('styles')
</head>
<body>
    <!-- Global search bar -->
    <header class="topbar" role="banner">
        <div class="search-wrap">
            <input id="globalSearch" class="search-input" type="search" placeholder="Search site..." aria-label="Search">
            <button class="search-btn" type="button" onclick="globalSearch()" aria-label="Search">Search</button>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <img src="{{ asset('IMG/PROTON.png') }}" alt="logo" />
            <h2>Admin</h2>
        </div>

        <ul class="links main-links">
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="material-symbols-outlined">dashboard</span>
                <a href="{{ route('admin.dashboard') }}">Laman Utama</a>
            </li>
            <li class="{{ request()->routeIs('admin.booking') ? 'active' : '' }}">
                <span class="material-symbols-outlined">calendar_month</span>
                <a href="{{ route('admin.booking') }}">Kemaskini Kalendar</a>
            </li>
            <li class="{{ request()->routeIs('admin.notification') ? 'active' : '' }}">
                <span class="material-symbols-outlined">notifications</span>
                <a href="{{ route('admin.notification') }}">Notifikasi Tempahan</a>
            </li>
            <li class="{{ request()->routeIs('admin.vehicle') ? 'active' : '' }}">
                <span class="material-symbols-outlined">library_books</span>
                <a href="{{ route('admin.vehicle') }}">Pengurusan Dokumen</a>
            </li>
        </ul>

        <ul class="links bottom-links">
            <li class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                <span class="material-symbols-outlined">person</span>
                <a href="{{ route('admin.profile') }}">Profil</a>
            </li>
            <li class="logout-link">
                <span class="material-symbols-outlined">logout</span>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Keluar</a>
            </li>
        </ul>
    </aside>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Main Content -->
    @yield('content')

    <!-- Logout confirmation modal -->
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

    <!-- Scripts -->
    <script>
        // Global search functionality
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

        // Logout modal functionality
        (function() {
            const logoutLink = document.querySelector('.logout-link a');
            if (!logoutLink) return;

            const modal = document.getElementById('logoutModal');
            const confirmBtn = document.getElementById('confirmLogout');
            const cancelBtn = document.getElementById('cancelLogout');
            const backdrop = modal.querySelector('.modal-backdrop');

            function openModal() {
                modal.setAttribute('aria-hidden', 'false');
                backdrop.addEventListener('click', closeModal);
            }

            function closeModal() {
                modal.setAttribute('aria-hidden', 'true');
                backdrop.removeEventListener('click', closeModal);
            }

            logoutLink.addEventListener('click', function(e) {
                e.preventDefault();
                openModal();
            });

            cancelBtn.addEventListener('click', closeModal);

            confirmBtn.addEventListener('click', function() {
                closeModal();
                setTimeout(() => {
                    document.getElementById('logout-form').submit();
                }, 100);
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
