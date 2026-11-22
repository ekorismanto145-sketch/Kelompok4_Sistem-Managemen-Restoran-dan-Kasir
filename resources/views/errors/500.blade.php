<x-app-layout title="500 · Kesalahan Server">
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-5xl font-extrabold text-[#E4572E]">500</p>
            <h1 class="mt-3 text-xl font-bold text-[#1E2A47]">Gagal Memuat Data</h1>
            <p class="mt-2 text-sm text-slate-500">Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.</p>
            <a href="{{ url('/') }}" class="mt-6 inline-block rounded-xl bg-[#1E2A47] px-6 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</x-app-layout>
