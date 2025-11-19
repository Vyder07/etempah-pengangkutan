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
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>

    @vite(['resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
    <!-- Toast Notifications -->
    <x-toast />

    <!-- Topbar -->
    <header class="fixed top-0 left-64 right-0 h-16 bg-white shadow-md z-40 flex items-center px-6">
        <div class="flex-1 max-w-xl">
            <div class="relative">
                <input
                    id="globalSearch"
                    type="search"
                    placeholder="@yield('search-placeholder', 'Cari...')"
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    aria-label="Search"
                >
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white shadow-2xl z-50 flex flex-col">
        <!-- Logo -->
        <div class="flex items-center gap-3 p-6 border-b border-gray-700">
            <img src="{{ asset('IMG/PROTON.png') }}" alt="logo" class="h-10 w-auto" />
            <h2 class="text-xl font-semibold">Admin</h2>
        </div>

        <!-- Main Navigation -->
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1 px-3">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span>Laman Utama</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.booking') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.booking') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <span class="material-symbols-outlined">calendar_month</span>
                        <span>Kemaskini Kalendar</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.notification') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.notification') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <span class="material-symbols-outlined">notifications</span>
                        <span>Notifikasi Tempahan</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.vehicle') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.vehicle') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <span class="material-symbols-outlined">library_books</span>
                        <span>Pengurusan Dokumen</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Bottom Navigation -->
        <nav class="border-t border-gray-700 py-4">
            <ul class="space-y-1 px-3">
                <li>
                    <a href="{{ route('admin.profile') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg transition {{ request()->routeIs('admin.profile') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <span class="material-symbols-outlined">person</span>
                        <span>Profil</span>
                    </a>
                </li>
                <li>
                    <a href="#"
                       onclick="event.preventDefault(); openLogoutModal();"
                       class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-red-600 hover:text-white transition">
                        <span class="material-symbols-outlined">logout</span>
                        <span>Log Keluar</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    <!-- Main Content -->
    <main class="ml-64 mt-16 min-h-screen p-6 bg-cover bg-center bg-fixed relative" style="background-image: url('{{ asset('IMG/BACKGROUNDADTEC.png') }}');">
        <!-- Overlay for better readability -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 via-purple-900/40 to-pink-900/40 pointer-events-none"></div>
        <div class="relative z-10">
            @yield('content')
        </div>
    </main>

    <!-- Logout Modal -->
    <div id="logoutModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeLogoutModal()"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <span class="material-symbols-outlined text-red-600">logout</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Sahkan Log Keluar
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Adakah anda pasti mahu log keluar?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="confirmLogout()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Log Keluar
                    </button>
                    <button type="button" onclick="closeLogoutModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Echo WebSocket listeners for admin
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                // Listen for new and updated bookings on admin-notifications channel
                window.Echo.channel('admin-notifications')
                    .listen('.booking.created', (e) => {
                        console.log('New booking notification for admin:', e);
                        if (typeof showToast === 'function') {
                            showToast(
                                'Tempahan Baharu!',
                                `${e.user.name} telah membuat tempahan ${e.vehicle_name} (${e.vehicle_type})`,
                                'success'
                            );
                        }

                        // Play notification sound (optional)
                        // new Audio('/sounds/notification.mp3').play();
                    })
                    .listen('.booking.updated', (e) => {
                        console.log('Booking updated by staff:', e);
                        if (typeof showToast === 'function') {
                            showToast(
                                'Tempahan Dikemaskini!',
                                `${e.user.name} telah mengemaskini tempahan ${e.vehicle_name}`,
                                'success'
                            );
                        }
                    });

                // Also listen on bookings channel for general updates
                window.Echo.channel('bookings')
                    .listen('.booking.created', (e) => {
                        console.log('Booking created on bookings channel:', e);
                    })
                    .listen('.booking.updated', (e) => {
                        console.log('Booking updated on bookings channel:', e);
                    });

                // Test channel for debugging
                window.Echo.channel('test-channel')
                    .listen('.test-event', (e) => {
                        console.log('Test event received:', e);
                        if (typeof showToast === 'function') {
                            showToast(
                                'Test Event Received',
                                e.message || 'WebSocket connection is working!',
                                'success'
                            );
                        }
                    });

                console.log('Echo listeners initialized for admin');
            } else {
                console.warn('Echo is not available. WebSocket notifications disabled.');
            }
        });

        // Global search functionality
        document.getElementById('globalSearch')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const q = this.value.trim();
                if (!q) return;
                const found = document.querySelectorAll('main *:not(script):not(style)');
                for (const el of found) {
                    if (el.textContent && el.textContent.toLowerCase().includes(q.toLowerCase())) {
                        el.scrollIntoView({behavior: 'smooth', block: 'center'});
                        el.style.outline = '3px solid #ffea00';
                        setTimeout(() => el.style.outline = '', 3000);
                        return;
                    }
                }
                alert('Tiada hasil ditemui pada halaman ini.');
            }
        });

        // Logout modal functions
        function openLogoutModal() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        function confirmLogout() {
            closeLogoutModal();
            setTimeout(() => {
                document.getElementById('logout-form').submit();
            }, 100);
        }

        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLogoutModal();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
