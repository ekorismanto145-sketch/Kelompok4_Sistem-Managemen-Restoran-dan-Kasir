<x-app-layout title="Lupa Password · PesanAja">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-center text-xl font-extrabold">Lupa Password</h1>
            <p class="mt-1 text-center text-xs text-slate-500">Masukkan email terdaftar untuk mendapatkan kode OTP.</p>

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#E4572E] focus:outline-none">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-[#E4572E] py-3 text-sm font-bold text-white hover:bg-[#d14922]">
                        Kirim Kode OTP
                    </button>
                </div>
            </form>

            <p class="mt-4 text-center text-xs text-slate-500">
                <a href="{{ route('login') }}" class="font-semibold text-[#E4572E]">← Kembali ke Login</a>
            </p>
        </div>
    </div>
</x-app-layout>
