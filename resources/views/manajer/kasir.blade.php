<x-app-layout title="Kelola Akun Kasir · PesanAja">
    <div class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('manajer.dashboard') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Dashboard</a>
                <a href="{{ route('manajer.menus.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Laporan</a>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-xl font-extrabold">Kelola Akun Kasir</h1>

                <form method="POST" action="{{ route('manajer.kasir.store') }}" class="mt-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-5 sm:grid-cols-3">
                    @csrf
                    <input type="text" name="name" placeholder="Nama kasir" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <input type="email" name="email" placeholder="Email kasir" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <input type="password" name="password" placeholder="Password (min 6)" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <div class="sm:col-span-3 sm:text-right">
                        <button type="submit" class="rounded-xl bg-[#E4572E] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#d14922]">
                            Tambah Akun Kasir
                        </button>
                    </div>
                </form>

                <div class="mt-6 space-y-2">
                    @foreach ($kasirs as $kasir)
                        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-4">
                            <div>
                                <p class="font-bold">{{ $kasir->name }}</p>
                                <p class="text-xs text-slate-500">{{ $kasir->email }}</p>
                            </div>
                            <form method="POST" action="{{ route('manajer.kasir.destroy', $kasir) }}" onsubmit="return confirm('Hapus akun kasir ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-100">Hapus</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
