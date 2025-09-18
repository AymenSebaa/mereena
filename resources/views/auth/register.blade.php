<x-guest-layout>

    <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" type="text" class="form-control rounded-pill" name="name"
                value="{{ old('name') }}" required autofocus>
            <x-input-error :messages="$errors->get('name')" class="text-danger small mt-1" />
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input id="phone" type="tel" class="form-control rounded-pill" name="phone"
                value="{{ old('phone') }}" required>
            <x-input-error :messages="$errors->get('phone')" class="text-danger small mt-1" />
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" class="form-control rounded-pill" name="email"
                value="{{ old('email') }}" required>
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
        
        <!-- Register button -->
        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                {{ __('Register') }}
            </button>
        </div>

        <!-- Already registered -->
        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="text-decoration-none">
                {{ __('Already registered?') }}
            </a>
        </div>

    </form>
</x-guest-layout>
