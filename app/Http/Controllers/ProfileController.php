<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller {
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function selectHotel(Request $request) {
        $request->validate(['hotel_id' => 'required|exists:hotels,id']);

        $user = $request->user();

        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $profile->hotel_id = $request->hotel_id;
        $profile->save();

        return redirect()->route('dashboard')->with('success', 'Hotel selected successfully!');
    }

    public function updateLocation(Request $request) {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $profile = $request->user()->profile;
        $profile->lat = $request->lat;
        $profile->lng = $request->lng;
        $profile->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Location updated',
            'data' => [
                'lat' => $profile->lat,
                'lng' => $profile->lng,
            ]
        ]);
    }
}
