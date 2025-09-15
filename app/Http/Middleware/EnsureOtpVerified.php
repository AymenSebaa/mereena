<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Otp;

class EnsureOtpVerified {
    public function handle(Request $request, Closure $next) {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Routes allowed without OTP
        $otpExemptRoutes = [
            'otp.verify',
            'otp.verify.submit',
            'otp.resend',
            'otp.remaining',
            'logout',
        ];

        // Get the latest OTP for the user
        $latestOtp = Otp::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->first();

        $otpVerified = $latestOtp && $latestOtp->used_at !== null && $latestOtp->expires_at->isFuture();

        if (!$otpVerified && !in_array($request->route()->getName(), $otpExemptRoutes)) {
            return redirect()->route('otp.verify');
        }

        return $next($request);
    }
}
