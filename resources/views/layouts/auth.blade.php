{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-theme="light" class="bg-gray-100">
    <div class="min-h-screen flex flex-col">

        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid px-4">
                <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                    <img src="{{ asset('icons/logo-round.png') }}" alt="Logo" width="40" class="me-2">
                    {{ config('app.name') }}
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow d-flex align-items-center justify-content-center p-4">
            <div class="w-100" style="max-width: 480px;">
                <div class="card shadow rounded-3">
                    <div class="card-body p-4">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
