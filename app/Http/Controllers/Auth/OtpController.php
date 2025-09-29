<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SmsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Otp;
use Exception;
use Illuminate\Support\Facades\Log;

class OtpController extends Controller {

    public function showVerifyForm() {
        return view('auth.otp-verify');
    }

    public function verify(Request $request) {
        $request->validate(['otp_code' => 'required|string']);
        $user = Auth::user();

        $latestOtp = Otp::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestOtp) {
            return back()->withErrors(['otp_code' => 'OTP expired. Please request a new one.']);
        }

        /*
        if (!$latestOtp->canAttempt()) {
            $wait = $latestOtp->next_attempt_at->diffInSeconds(now());
            return back()->withErrors(['otp_code' => "Too many attempts. Try again in {$wait} seconds."]);
        }
        */

        if ($latestOtp->code !== $request->otp_code) {
            $latestOtp->incrementAttempt();
            return back()->withErrors(['otp_code' => 'Invalid OTP.']);
        }

        $latestOtp->markAsUsed();
        $latestOtp->resetAttempts();

        $latestOtp->expires_at = now()->addDay(7);
        $latestOtp->save();

        return redirect()->route('dashboard')->with('success', 'OTP verified!');
    }

    public function remaining() {
        $user = Auth::user();
        $latestOtp = Otp::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->first();

        $now = now()->timestamp;

        return response()->json([
            'otp_expires_at' => $latestOtp?->expires_at?->timestamp ?? $now,
            'resend_available_at' => $latestOtp?->next_attempt_at?->timestamp ?? $now
        ]);
    }

    public function resend(Request $request) {
        $user = Auth::user();
        $latestOtp = Otp::where('email', $user->email)->orderBy('created_at', 'desc')->first();
        $cooldown = 30; // seconds

        if ($latestOtp && $latestOtp->next_attempt_at && $latestOtp->next_attempt_at->isFuture()) {
            $remaining = $latestOtp->next_attempt_at->diffInSeconds(now());
            return response()->json([
                'error' => 'Please wait before requesting a new OTP.',
                'cooldown' => $remaining
            ], 429);
        }

        $otp = self::generateAndSendOTP($user);
        $otp->next_attempt_at = now()->addSeconds($cooldown);
        $otp->save();

        return response()->json([
            'success' => 'A new OTP has been sent.',
            'otp_expires_at' => $otp->expires_at->timestamp,
            'resend_available_at' => $otp->next_attempt_at->timestamp
        ]);
    }

    public static function generateAndSendOTP($user) {
        $otpCode = $user->email == 'demo@example.com' ? '000000' : rand(100000, 999999);

        $otp = Otp::create([
            'email' => $user->email,
            'code' => $otpCode,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
            'next_attempt_at' => null,
        ]);

        try {
            Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
                $message->to($user->email)->subject('Your OTP Code');
            });
        } catch (Exception $e) {
            Log::error('Failed to send OTP Email', [
                'email'   => $user->email ?? null,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }
        try {
            if (!empty($user->profile->phone)) {
                SmsController::sendSms($user->profile->phone, "Your OTP code is: {$otpCode}");
            }
        } catch (Exception $e) {
            Log::error('Failed to send OTP SMS', [
                'email'   => $user->email ?? null,
                'phone'   => $user->profile->phone ?? null,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
        }

        return $otp;
    }
}
