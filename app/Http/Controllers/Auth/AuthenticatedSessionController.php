<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use App\Models\User;
use App\Mail\OTPMail;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * Modified untuk OTP flow
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. VALIDATE CREDENTIALS (tapi jangan login dulu)
        $credentials = $request->only('email', 'password');
        
        if (!Auth::validate($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        // 2. FIND USER & GENERATE OTP
        $user = User::where('email', $request->email)->first();
        $otp = $user->generateOTP(); // Method yang sudah kita buat di User model

        // 3. SEND OTP EMAIL
        try {
            Mail::to($user->email)->send(new OTPMail($user, $otp));
        } catch (\Exception $e) {
            // Kembalikan ke pesan error normal
            return back()->withErrors([
                'email' => 'Gagal mengirim email OTP. Silakan coba lagi.',
            ])->onlyInput('email');
        }

        // 4. STORE USER ID IN SESSION (untuk OTP verification)
        $request->session()->put('otp_user_id', $user->id);

        // 5. REDIRECT TO OTP VERIFICATION PAGE
        return redirect()->route('otp.verify')->with('status', 'Kode OTP telah dikirim ke email Anda. Silakan cek inbox/spam.');
    }

    /**
     * Show OTP verification form
     */
    public function showOTPForm(): View
    {
        // Check if user session exists
        if (!session('otp_user_id')) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please login again.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP and login user
     */
    public function verifyOTP(Request $request): RedirectResponse
    {
        $request->validate([
            'otp_code' => 'required|digits:6',
        ]);

        // Get user from session
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        // Verify OTP
        if (!$user->verifyOTP($request->otp_code)) {
            return back()->withErrors([
                'otp_code' => $user->isOTPExpired() ? 'Kode OTP telah kedaluwarsa.' : 'Kode OTP salah.',
            ]);
        }

        // Clear OTP and login
        $user->clearOTP();
        $request->session()->forget('otp_user_id');
        
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request): RedirectResponse
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
        }

        // Generate new OTP
        $otp = $user->generateOTP();

        // Send OTP email
        try {
            Mail::to($user->email)->send(new OTPMail($user, $otp));
            return back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            return back()->withErrors(['otp_code' => 'Gagal mengirim email OTP. Silakan coba lagi.']);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
