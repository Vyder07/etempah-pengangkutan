<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'Admin Login')</title>
    <link rel="stylesheet" href="{{ asset('css/admin-auth.css') }}">
    @stack('styles')
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>
