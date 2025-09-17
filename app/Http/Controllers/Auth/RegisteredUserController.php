<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Hotel;
use App\Models\Profile;
use App\Models\Type;
use App\Models\User;
use App\Services\UserRegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller {
    protected $registrationService;


    public function __construct(UserRegistrationService $registrationService) {
        $this->registrationService = $registrationService;
    }

    /**
     * Display the registration view.
     */
    public function create(): View {
        $data = [];
        return view('auth.register', $data);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'max:255'],
            'role_id'  => ['nullable', 'exists:roles,id'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:profiles,phone'],
        ]);

        $this->registrationService->register($request);

        return redirect(route('dashboard'));
    }
}
