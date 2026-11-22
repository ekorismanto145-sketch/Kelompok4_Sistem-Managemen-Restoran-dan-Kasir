<x-app-layout title="Kelola Menu · PesanAja">
    <div class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('manajer.dashboard') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Dashboard</a>
                <a href="{{ route('manajer.menus.index') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Laporan</a>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8">
            <div class="mx-auto max-w-5xl">
                <h1 class="text-xl font-extrabold">Kelola Menu</h1>

                {{-- Form Tambah Menu --}}
                <form method="POST" action="{{ route('manajer.menus.store') }}" class="mt-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-5 sm:grid-cols-2">
                    @csrf
                    <input type="text" name="name" placeholder="Nama menu" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <select name="category_id" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="price" placeholder="Harga (Rp)" min="0" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <input type="url" name="image_url" placeholder="URL Foto (opsional)" class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <input type="text" name="description" placeholder="Deskripsi singkat" class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm sm:col-span-2">
                    <div class="flex items-center gap-5 text-sm">
                        <label class="flex items-center gap-2"><input type="checkbox" name="is_available" value="1" checked> Tersedia</label>
                        <label class="flex items-center gap-2"><input type="checkbox" name="has_spicy_level" value="1"> Punya Level Pedas</label>
                    </div>
                    <div class="sm:text-right">
                        <button type="submit" class="rounded-xl bg-[#E4572E] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#d14922]">
                            Tambah Menu
                        </button>
                    </div>
                </form>

                {{-- Daftar Menu --}}
                <div class="mt-6 space-y-2">
                    @foreach ($menus as $menu)
                        <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                            <div>
                                <p class="font-bold">{{ $menu->name }} <span class="text-xs font-normal text-slate-400">· {{ $menu->category->name }}</span></p>
                                <p class="text-xs text-slate-500">
                                    <x-price :amount="$menu->price" class="font-semibold text-[#E4572E]" />
                                    · {{ $menu->is_available ? 'Tersedia' : 'Habis' }}
                                    @if ($menu->has_spicy_level) · Level 0–8 @endif
                                </p>
                            </div>
                            <form method="POST" action="{{ route('manajer.menus.destroy', $menu) }}" onsubmit="return confirm('Hapus menu ini?')">
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
