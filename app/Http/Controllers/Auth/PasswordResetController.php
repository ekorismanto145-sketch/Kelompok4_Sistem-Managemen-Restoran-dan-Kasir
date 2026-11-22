<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showForgot(): View
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Email tidak terdaftar.',
        ]);

        $otp = (string) random_int(100000, 999999);

        $request->session()->put([
            'otp_email' => $data['email'],
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinute()->timestamp,
        ]);

        return redirect()->route('password.otp.form');
    }

    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('otp_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', [
            'email' => $request->session()->get('otp_email'),
            'sandboxOtp' => $request->session()->get('otp_code'),
            'expiresAt' => (int) $request->session()->get('otp_expires_at', 0),
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'digits:6']]);

        $expectedOtp = $request->session()->get('otp_code');
        $expiresAt = (int) $request->session()->get('otp_expires_at', 0);
        $email = $request->session()->get('otp_email');

        if (! $email || ! $expectedOtp || now()->timestamp > $expiresAt) {
            $request->session()->forget(['otp_code', 'otp_expires_at', 'otp_email']);

            return redirect()->route('password.request')->withErrors(['email' => 'Kode OTP sudah kadaluarsa.']);
        }

        if ($request->string('otp')->toString() !== $expectedOtp) {
            return back()->withErrors(['otp' => 'Kode OTP tidak sesuai.']);
        }

        $request->session()->forget(['otp_code', 'otp_expires_at', 'otp_email']);
        $request->session()->put('password_reset_email', $email);

        return redirect()->route('password.reset.form');
    }

    public function showResetForm(Request $request): View|RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $email = $request->session()->get('password_reset_email');

        if (! $email) {
            return redirect()->route('password.request');
        }

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        $user = User::where('email', $email)->firstOrFail();
        $user->update(['password' => Hash::make($data['password'])]);

        $request->session()->forget('password_reset_email');

        return redirect()->route('login')->with('success', 'Password berhasil diperbarui. Silakan masuk dengan password baru.');
    }
}
