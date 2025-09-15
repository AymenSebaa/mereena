<x-guest-layout>
    <!-- Logo -->
    <div class="text-center mb-4 d-flex justify-content-center logo">
        <img src="{{ asset('icons/iatf.jpg') }}" alt="IATF Logo" class="img-fluid w-100">
    </div>

    <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
        @csrf
        <!-- Category -->
        <div class="mb-3">
            <label for="category" class="form-label">{{ __('Category') }}</label>
            <select id="category" name="category" class="form-select rounded-pill" required>
                <option value="">{{ __('Select your category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('category')" class="text-danger small mt-1" />
        </div>

        <!-- Extra input for "Other" -->
        <div class="mb-3 d-none" id="other-category-wrapper">
            <label for="other_category" class="form-label">{{ __('Specify Category') }}</label>
            <input id="other_category" type="text" class="form-control rounded-pill" name="other_category"
                value="{{ old('other_category') }}">
            <x-input-error :messages="$errors->get('other_category')" class="text-danger small mt-1" />
        </div>

        <!-- Country -->
        <div class="mb-3">
            <label for="country_id" class="form-label">{{ __('Country') }}</label>
            <select id="country_id" name="country_id" class="form-select rounded-pill" required>
                <option value="">{{ __('Select your country') }}</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                        {{ $country->name_en }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('country_id')" class="text-danger small mt-1" />
        </div>

        <!-- Hotel -->
        <div class="mb-3">
            <label for="hotel_id" class="form-label">{{ __('Hotel') }}</label>
            <select id="hotel_id" name="hotel_id" class="form-select rounded-pill" required>
                <option value="">{{ __('Select your hotel') }}</option>
                @foreach ($hotels as $hotel)
                    <option value="{{ $hotel->id }}" {{ old('hotel_id') == $hotel->id ? 'selected' : '' }}>
                        {{ $hotel->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('hotel_id')" class="text-danger small mt-1" />
        </div>

        <hr class="m-3">

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" type="text" class="form-control rounded-pill" name="name"
                value="{{ old('name') }}" required autofocus>
            <x-input-error :messages="$errors->get('name')" class="text-danger small mt-1" />
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" class="form-control rounded-pill" name="email"
                value="{{ old('email') }}" required>
            <x-input-error :messages="$errors->get('email')" class="text-danger small mt-1" />
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input id="phone" type="tel" class="form-control rounded-pill" name="phone"
                value="{{ old('phone') }}" required>
            <x-input-error :messages="$errors->get('phone')" class="text-danger small mt-1" />
        </div>

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

        <!-- Reservation link -->
        <div class="text-center mt-4">
            <a href="{{ route('flights.book') }}" class="text-decoration-none">
                {{ __('Book a Flight') }}
            </a>
        </div>
    </form>
</x-guest-layout>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const categorySelect = document.getElementById('category');
        const otherWrapper = document.getElementById('other-category-wrapper');

        function toggleOther() {
            if (categorySelect.value === "Other") {
                otherWrapper.classList.remove("d-none");
            } else {
                otherWrapper.classList.add("d-none");
            }
        }

        categorySelect.addEventListener("change", toggleOther);
        toggleOther(); // run once on page load
    });
</script>
