<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Mereena')</title>
    <link rel="icon" href="{{ asset('icons/logo-round.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Bootstrap CSS + Bootstrap Icons + DataTables CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.3/css/dataTables.dataTables.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- PWA: Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#e70147">

    <!-- iOS Support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="TFA">

    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-icon-180.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('icons/apple-icon-152.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('icons/apple-icon-120.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('icons/apple-icon-76.png') }}">

    <link rel="apple-touch-startup-image" href="{{ asset('screenshots/wide.png') }}"
        media="(device-width: 320px) and (device-height: 568px)">

    {{-- Custom CSS --}}
    <link href="{{ asset('css/custom-style.css') }}" rel="stylesheet">

    @if (auth()->check() && auth()->user()->profile->role_id == 10)
        <style>
            :root {
                --primary: #e70147 !important;
                --primary-dark: #c40038 !important;
                /* optional darker shade */
            }
        </style>
    @endif

    <script src="{{ asset('js/sanitize.js') }}"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    {{-- jQuery + Bootstrap JS + DataTables JS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.3/js/dataTables.min.js"></script>

</head>
@php
    $permissions = auth()->user()->profile->role->permissions ?? [];
@endphp

<body>
    {{-- @include('partials.install-pwa') --}}
    @include('partials.update-location')

    @include('partials.toaster')

    <div class="app-container">
        {{-- Mobile bottom bar --}}
        @include('partials.bottom-bar')

        {{-- Sidebar --}}
        @include('partials.sidebar')

        <main class="main-content">
            <div class="content-area">
                @yield('content')
            </div>

            {{--
            <div class="footer glass-footer">
                <p>Made with <i class="bi bi-heart-fill text-accent"></i> by TFA Team | Transport For Algiers</p>
            </div>
            --}}
        </main>
    </div>

    @stack('scripts')

    <script>
        function formatDateTime(dateStr) {
            if (!dateStr) return '-';

            const date = new Date(dateStr);
            if (isNaN(date.getTime())) return '-'; // Invalid date

            const pad = n => String(n).padStart(2, '0');

            const year = date.getFullYear();
            const month = pad(date.getMonth() + 1); // Months are 0-indexed
            const day = pad(date.getDate());
            const hours = pad(date.getHours());
            const minutes = pad(date.getMinutes());
            const seconds = pad(date.getSeconds());

            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        }

        if ("serviceWorker" in navigator) {
            window.addEventListener("load", () => {
                navigator.serviceWorker
                    .register("app.js")
                    .then(res => console.log("service worker registered"))
                    .catch(err => console.log("service worker not registered", err))
            })
        }

        const pushPublicKey = `{{ env('PUSH_PUBLIC_KEY') }}`;
        const pushURL = `{{ route('push') }}`;
        const csrf = '{{ csrf_token() }}';

        // Theme toggle functionality
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = themeToggle.querySelector('i');
        const body = document.body;

        // Check for saved theme preference or respect OS preference
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            body.setAttribute('data-theme', 'dark');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        // Toggle theme on button click
        themeToggle.addEventListener('click', function() {
            if (body.getAttribute('data-theme') === 'dark') {
                body.setAttribute('data-theme', 'light');
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>

    <script src="{{ asset('push.js') }}"></script>
</body>

</html>
