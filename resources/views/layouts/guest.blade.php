<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('icons/logo-round.png') }}" type="image/x-icon">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f5f5, #ffffff, #eaeaea);
            background-size: 400% 400%;
            animation: gradientShift 12s ease infinite;
            color: #333;
            transition: background 0.6s ease, color 0.6s ease;
        }

        body.dark-mode {
            background: linear-gradient(135deg, #0f0f0f, #1a1a1a, #2e2e2e);
            color: #fff;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 1.5rem;
            padding: 2.2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 0.8s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: background 0.6s ease, color 0.6s ease, box-shadow 0.6s ease;
        }

        body.dark-mode .glass-card {
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-primary {
            background-color: #e70147;
            border: none;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #bf013a;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(231, 1, 71, 0.4);
        }

        a {
            color: #e70147;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        a:hover {
            color: #ff2e6a;
            text-decoration: underline;
        }

        .form-label {
            font-weight: 500;
        }

        .form-control,
        .form-select {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.15);
            color: #333;
            transition: background 0.6s ease, color 0.6s ease, border 0.6s ease;
        }

        .form-control:focus,
        .form-select:focus {
            background: #fff;
            border-color: #e70147;
            box-shadow: 0 0 0 0.25rem rgba(231, 1, 71, 0.25);
        }

        body.dark-mode .form-control,
        body.dark-mode .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        body.dark-mode .form-control:focus,
        body.dark-mode .form-select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #e70147;
            color: #fff;
        }

        .theme-toggle {
            position: absolute;
            top: 1rem;
            right: 1rem;
            z-index: 100;
            background: rgba(255, 255, 255, 0.6);
            border: none;
            border-radius: 50%;
            padding: 0.6rem 0.8rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        body.dark-mode .theme-toggle {
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(25px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            border-radius: 8px;
            overflow: hidden;
        }

        .backdrop {
            background-image: url('{{ asset('icons/iatf.jpg') }}');
            background-size: contain;
            background-position: center;
            position: fixed;
            top: -50%;
            left: -50%;
            width: 160%;
            height: 160%;
            opacity: 0.05;
            z-index: -1;
            transform: rotate(15deg);
        }

        /* remove default backdrop blocking but keep z-index layer */
        .modal-backdrop {
            background: transparent !important;
            pointer-events: none !important;
            /* allow clicking through */
        }
    </style>
</head>

<body>
    <div class="backdrop"></div>

    <!-- Theme toggle button -->
    <button id="themeToggle" class="theme-toggle"> 🌙 </button>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div id="wrapper" class="glass-card w-100" style="max-width: 420px;">
            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toggleBtn = document.getElementById('themeToggle');
        const body = document.body;

        // Load saved theme from localStorage
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            toggleBtn.textContent = '☀️';
        }

        toggleBtn.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            if (body.classList.contains('dark-mode')) {
                toggleBtn.textContent = '☀️';
                localStorage.setItem('theme', 'dark');
            } else {
                toggleBtn.textContent = '🌙';
                localStorage.setItem('theme', 'light');
            }
        });
    </script>
</body>

</html>
