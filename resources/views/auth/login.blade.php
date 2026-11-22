<x-app-layout title="Masuk · PesanAja">
    <div class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-sm rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h1 class="text-center text-2xl font-extrabold">
                Pesan<span class="text-[#E4572E]">Aja</span>
            </h1>
            <p class="mt-1 text-center text-xs text-slate-500">Masuk untuk memesan atau mengelola antrian</p>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#E4572E] focus:outline-none">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ show: false }">
                    <label class="text-xs font-semibold text-slate-600">Password</label>
                    <div style="position: relative;" class="mt-1">
                        <input :type="show ? 'text' : 'password'" name="password" required
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 pr-10 text-sm focus:border-[#E4572E] focus:outline-none">
                        <button type="button" @click="show = !show" aria-label="Tampilkan password"
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);"
                                class="z-10 text-slate-500 hover:text-slate-700">
                            <svg x-show="!show" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="show" x-cloak class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full rounded-xl bg-[#E4572E] py-3 text-sm font-bold text-white hover:bg-[#d14922]">
                        Masuk
                    </button>
                </div>
            </form>

            <p class="mt-4 text-center text-xs text-slate-500">
                Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-[#E4572E]">Daftar</a>
            </p>
        </div>
    </div>
</x-app-layout>
