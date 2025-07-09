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
        // Check if user has valid session
        if (!session('otp_user_id')) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi expired. Silakan login ulang.']);
        }

        return view('auth.verify-otp');
    }

    /**
     * Handle OTP verification
     */
    public function verifyOTP(Request $request): RedirectResponse
    {
        // DEBUG: Log untuk debugging
        \Log::info('OTP Verification Debug', [
            'input_otp' => $request->otp,
            'user_id' => session('otp_user_id'),
            'all_input' => $request->all()
        ]);

        // 1. VALIDATE OTP INPUT
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        // 2. GET USER FROM SESSION
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi expired. Silakan login ulang.']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // 3. VERIFY OTP
        if (!$user->verifyOTP($request->otp)) {
            return back()->withErrors([
                'otp' => 'Kode OTP salah atau sudah expired.',
            ]);
        }

        // 4. OTP VALID - LOGIN USER
        Auth::login($user);
        $user->clearOTP(); // Clear OTP setelah berhasil login

        // 5. CLEAR SESSION & REGENERATE
        $request->session()->forget('otp_user_id');
        $request->session()->regenerate();

        // 6. REDIRECT TO DASHBOARD WITH PROPER SESSION HANDLING
        $request->session()->regenerate();
                
        return redirect()->route('dashboard')->with([
            'success' => 'Login berhasil! Selamat datang di Our Memories.',
            'first_login' => true
        ]);
    }

    /**
     * Resend OTP email
     */
    public function resendOTP(Request $request): RedirectResponse
    {
        $userId = session('otp_user_id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi expired. Silakan login ulang.']);
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Generate new OTP
        $otp = $user->generateOTP();

        // Send email
        try {
            Mail::to($user->email)->send(new OTPMail($user, $otp));
            return back()->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => 'Gagal mengirim email OTP. Silakan coba lagi.']);
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