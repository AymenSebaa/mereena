<?php

namespace App\Services;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\Auth\OtpController;

class UserRegistrationService {
    public function register($request) {
        $request->merge([
            'email' => strtolower(trim($request->email)),
            'name'  => trim($request->name),
        ]);

        $category = $request->category === "Other" ? $request->other_category : $request->category;

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // ✅ Secure hashing
        ]);

        $roleId = $request->role_id ?? 10; // default guest

        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $profile->role_id    = $roleId;
        $profile->country_id = v($request->country_id);
        $profile->site_id    = v($request->site_id);
        $profile->category   = v($category);
        $profile->phone      = v($request->phone);
        $profile->address      = v($request->address);
        $profile->save();

        event(new Registered($user));
        Auth::login($user);
        // OtpController::generateAndSendOTP($user);

        return $user;
    }
}
