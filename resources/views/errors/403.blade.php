<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-red-500 via-pink-500 to-purple-600 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-8 md:p-12 max-w-2xl w-full text-center">
        <!-- Icon -->
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-100 mb-4">
                <span class="material-symbols-outlined text-red-600 text-6xl">block</span>
            </div>
        </div>

        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-gray-800 mb-2">403</h1>

        <!-- Error Message -->
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Akses Ditolak</h2>

        <p class="text-gray-600 text-lg mb-8">
            Maaf, anda tidak mempunyai kebenaran untuk mengakses halaman ini.
            @if($exception->getMessage())
                <br><span class="text-red-600 font-medium">{{ $exception->getMessage() }}</span>
            @endif
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="javascript:history.back()"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-semibold rounded-lg transition shadow-lg">
                <span class="material-symbols-outlined">arrow_back</span>
                Kembali
            </a>

            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition shadow-lg">
                        <span class="material-symbols-outlined">home</span>
                        Dashboard Admin
                    </a>
                @elseif(auth()->user()->role === 'staff')
                    <a href="{{ route('staff.dashboard') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition shadow-lg">
                        <span class="material-symbols-outlined">home</span>
                        Dashboard Staf
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold rounded-lg transition shadow-lg">
                    <span class="material-symbols-outlined">login</span>
                    Log Masuk
                </a>
            @endauth
        </div>

        <!-- Additional Info -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500">
                Jika anda percaya ini adalah kesilapan, sila hubungi pentadbir sistem.
            </p>
        </div>
    </div>
</body>
</html>
