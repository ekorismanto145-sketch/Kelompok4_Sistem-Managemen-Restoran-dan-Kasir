<x-app-layout title="Kelola Kategori · PesanAja">
    <div class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('manajer.dashboard') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Dashboard</a>
                <a href="{{ route('manajer.menus.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Laporan</a>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8">
            <div class="mx-auto max-w-3xl">
                <h1 class="text-xl font-extrabold">Kelola Kategori</h1>

                <form method="POST" action="{{ route('manajer.categories.store') }}" class="mt-5 flex gap-2">
                    @csrf
                    <input type="text" name="name" placeholder="Nama kategori baru..." required
                           class="flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm focus:border-[#E4572E] focus:outline-none">
                    <button type="submit" class="rounded-xl bg-[#E4572E] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#d14922]">
                        Tambah
                    </button>
                </form>
                @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                <div class="mt-6 space-y-2">
                    @foreach ($categories as $category)
                        <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                            <form method="POST" action="{{ route('manajer.categories.update', $category) }}" class="flex flex-1 items-center gap-2">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $category->name }}" required
                                       class="flex-1 rounded-xl border border-slate-200 px-3 py-1.5 text-sm font-semibold focus:border-[#E4572E] focus:outline-none">
                                <span class="text-xs text-slate-400">{{ $category->menus_count }} menu</span>
                                <button type="submit" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold hover:bg-slate-200">Simpan</button>
                            </form>
                            <form method="POST" action="{{ route('manajer.categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
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
