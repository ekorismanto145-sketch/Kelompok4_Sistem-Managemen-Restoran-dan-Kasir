<x-app-layout title="Verifikasi OTP · PesanAja">
    <div x-data="{
        secondsLeft: Math.max(0, {{ $expiresAt }} - Math.floor(Date.now() / 1000)),
        init() {
            const timer = setInterval(() => {
                if (this.secondsLeft > 0) this.secondsLeft--;
                else clearInterval(timer);
            }, 1000);
        }
    }" class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-center text-xl font-extrabold">Verifikasi OTP</h1>
            <p class="mt-1 text-center text-xs text-slate-500">Akun: <span class="font-semibold text-[#1E2A47]">{{ $email }}</span></p>

            {{-- Virtual OTP Sandbox Banner --}}
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-3 text-center">
                <p class="text-[11px] font-bold uppercase tracking-wide text-amber-800">Kode OTP</p>
                <p class="mt-1 text-2xl font-extrabold tracking-widest text-[#1E2A47]">{{ $sandboxOtp }}</p>
                <p x-show="secondsLeft > 0" class="mt-0.5 text-[11px] text-amber-700">
                    Berlaku <span class="font-bold" x-text="secondsLeft"></span> detik lagi
                </p>
                <p x-show="secondsLeft <= 0" x-cloak class="mt-1 text-xs font-bold text-red-600">
                    Kode OTP sudah kadaluarsa
                </p>
            </div>

            <form method="POST" action="{{ route('password.otp.verify') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-slate-600">Masukkan 6 Digit Kode OTP</label>
                    <input type="text" name="otp" maxlength="6" inputmode="numeric" required autofocus :disabled="secondsLeft <= 0"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-center text-lg font-bold tracking-widest focus:border-[#E4572E] focus:outline-none disabled:bg-slate-100">
                    @error('otp') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" :disabled="secondsLeft <= 0"
                            class="w-full rounded-xl bg-[#E4572E] py-3 text-sm font-bold text-white hover:bg-[#d14922] disabled:cursor-not-allowed disabled:bg-slate-300">
                        Verifikasi OTP
                    </button>
                </div>
            </form>

            <p class="mt-4 text-center text-xs text-slate-500">
                <a href="{{ route('password.request') }}" class="font-semibold text-[#E4572E]">← Minta OTP Ulang</a>
            </p>
        </div>
    </div>
</x-app-layout>
