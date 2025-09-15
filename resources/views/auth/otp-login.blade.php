<x-guest-layout>
    <!-- Logo -->
    <div class="text-center mb-4 d-flex justify-content-center logo">
        <img src="{{ asset('icons/iatf.jpg') }}" alt="IATF Logo" class="img-fluid w-100">
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3 text-success small text-center" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" class="form-control rounded-pill" name="email"
                value="{{ old('email') }}" required autofocus autocomplete="username">
            <x-input-error :messages="$errors->get('email')" class="text-danger small mt-1" />
        </div>

        <!-- Password
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control rounded-pill" name="password" required
                autocomplete="current-password">
            <x-input-error :messages="$errors->get('password')" class="text-danger small mt-1" />
        </div>
        -->

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input rounded" name="remember">
            <label for="remember_me" class="form-check-label">
                {{ __('Remember me') }}
            </label>
        </div>

        <!-- Login button -->
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                {{ __('Log in') }}
            </button>
        </div>

        <!-- Register link -->
        <div class="text-center mt-4">
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="text-decoration-none">
                    {{ __("Don't have an account? Register") }}
                </a>
            @endif
        </div>

        <!-- Reservation link -->
        <div class="text-center mt-4">
            <a href="{{ route('flights.book') }}" class="text-decoration-none">
                {{ __("Book a Flight") }}
            </a>
        </div>
    </form>
</x-guest-layout>
