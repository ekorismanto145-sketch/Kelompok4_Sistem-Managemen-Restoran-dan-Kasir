<x-app-layout title="Kelola Menu · PesanAja">
    <style>
        .menu-file-input::file-selector-button {
            background-color: #e2e8f0;
            color: #1e293b;
            border: 0;
            border-radius: 0.5rem;
            padding: 0.45rem 0.75rem;
            margin-right: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        .menu-file-input::file-selector-button:hover {
            background-color: #475569;
            color: #ffffff;
        }
    </style>
    <div class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('manajer.dashboard') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Dashboard</a>
                <a href="{{ route('manajer.menus.index') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Laporan</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left font-semibold hover:opacity-90">Keluar</button>
                </form>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8">
            <div class="mx-auto max-w-5xl">
                <h1 class="text-xl font-extrabold">Kelola Menu</h1>

                @if ($errors->any())
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-xs text-red-700">
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form Tambah Menu --}}
                <form method="POST" action="{{ route('manajer.menus.store') }}" enctype="multipart/form-data" class="mt-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-5 sm:grid-cols-2">
                    @csrf
                    <input type="text" name="name" placeholder="Nama menu" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <select name="category_id" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="price" placeholder="Harga (Rp)" min="0" inputmode="numeric" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <input type="number" name="stock" placeholder="Stok" min="0" inputmode="numeric" required class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm">
                    <input type="file" name="image" accept="image/*" class="menu-file-input w-full rounded-xl border border-slate-200 bg-slate-50 p-1.5 text-xs text-slate-600">
                    <input type="text" name="description" placeholder="Deskripsi singkat" class="rounded-xl border border-slate-200 px-3.5 py-2 text-sm sm:col-span-2">
                    <div class="sm:col-span-2 flex flex-wrap items-center gap-x-6 gap-y-2.5 pt-1 text-sm">
                        <label class="flex cursor-pointer items-center gap-2 whitespace-nowrap"><input type="checkbox" name="is_available" value="1" checked class="rounded border-slate-300 text-[#E4572E] focus:ring-[#E4572E]"> Tersedia</label>
                        <label class="flex cursor-pointer items-center gap-2 whitespace-nowrap"><input type="checkbox" name="has_spicy_level" value="1" class="rounded border-slate-300 text-[#E4572E] focus:ring-[#E4572E]"> Punya Level Pedas</label>
                        <label class="flex cursor-pointer items-center gap-2 whitespace-nowrap"><input type="checkbox" name="has_temperature_option" value="1" class="rounded border-slate-300 text-[#E4572E] focus:ring-[#E4572E]"> Opsi Hot / Ice</label>
                    </div>
                    <div class="sm:col-span-2 sm:text-right">
                        <button type="submit" class="w-full rounded-xl bg-[#E4572E] px-5 py-2.5 text-sm font-bold text-white hover:bg-[#d14922] sm:w-auto">
                            Tambah Menu
                        </button>
                    </div>
                </form>

                {{-- Daftar Menu (Grid seperti Customer) --}}
                <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($menus as $menu)
                        <div x-data="{ editing: false }" class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-3 shadow-sm {{ !$menu->is_available ? 'opacity-75' : '' }}">
                            <div>
                                <div class="relative mb-2.5 aspect-square overflow-hidden rounded-xl bg-slate-100">
                                    @if ($menu->image_url)
                                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" referrerpolicy="no-referrer" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-xs font-medium text-slate-400">
                                            {{ $menu->category->name }}
                                        </div>
                                    @endif
                                    @if (!$menu->is_available)
                                        <span class="absolute inset-0 flex items-center justify-center bg-slate-900/50 text-xs font-bold tracking-wider text-white">HABIS</span>
                                    @endif
                                </div>

                                <p class="text-[11px] font-semibold text-slate-400">{{ $menu->category->name }}</p>
                                <h3 class="text-sm font-bold leading-snug">{{ $menu->name }}</h3>
                                @if ($menu->has_spicy_level || $menu->has_temperature_option)
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ collect([$menu->has_spicy_level ? 'Level 0–8' : null, $menu->has_temperature_option ? 'Hot / Ice' : null])->filter()->implode(' · ') }}
                                    </p>
                                @elseif ($menu->description)
                                    <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ $menu->description }}</p>
                                @endif
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between gap-2">
                                    <x-price :amount="$menu->price" class="text-sm font-bold text-[#E4572E]" />
                                    @php
                                        $stockBadgeClass = match (true) {
                                            $menu->stock > 100 => 'bg-emerald-100 text-emerald-800',
                                            $menu->stock >= 50 => 'bg-amber-100 text-amber-800',
                                            default => 'bg-red-100 text-red-800',
                                        };
                                    @endphp
                                    <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $stockBadgeClass }}">
                                        Stok: {{ $menu->stock }}
                                    </span>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <button type="button" @click="editing = !editing" class="flex-1 rounded-lg bg-slate-100 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200">
                                        <span x-text="editing ? 'Batal' : 'Edit'"></span>
                                    </button>
                                    <form method="POST" action="{{ route('manajer.menus.destroy', $menu) }}" onsubmit="return confirm('Hapus menu ini?')" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-lg py-1.5 text-xs font-semibold hover:opacity-90">Hapus</button>
                                    </form>
                                </div>
                            </div>

                            <form x-show="editing" x-cloak method="POST" action="{{ route('manajer.menus.update', $menu) }}" enctype="multipart/form-data"
                                  class="mt-3 space-y-2 border-t border-slate-100 pt-3">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="mb-0.5 block text-[11px] font-semibold text-slate-600">Nama Menu</label>
                                    <input type="text" name="name" value="{{ $menu->name }}" placeholder="Nama Menu" required class="w-full rounded-xl border border-slate-200 px-2.5 py-1.5 text-xs">
                                </div>
                                <div>
                                    <label class="mb-0.5 block text-[11px] font-semibold text-slate-600">Kategori</label>
                                    <select name="category_id" required class="w-full rounded-xl border border-slate-200 px-2.5 py-1.5 text-xs">
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" @selected($menu->category_id === $cat->id)>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-1 flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-600">Stok: {{ $menu->stock }}</span>
                                    @if ($menu->stock < 50)
                                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-800">Stok Menipis</span>
                                    @endif
                                </div>
                                <div>
                                    <label class="mb-0.5 block text-[11px] font-semibold text-slate-600">Harga (Rp)</label>
                                    <input type="number" name="price" value="{{ $menu->price }}" placeholder="Harga (Rp)" min="0" inputmode="numeric" required class="w-full rounded-xl border border-slate-200 px-2.5 py-1.5 text-xs">
                                </div>
                                <div>
                                    <label class="mb-0.5 block text-[11px] font-semibold text-slate-600">Stok</label>
                                    <input type="number" name="stock" value="{{ $menu->stock }}" placeholder="Stok" min="0" inputmode="numeric" required class="w-full rounded-xl border border-slate-200 px-2.5 py-1.5 text-xs">
                                </div>
                                <div>
                                    <label class="mb-0.5 block text-[11px] font-semibold text-slate-600">Gambar Menu</label>
                                    <input type="file" name="image" accept="image/*" class="menu-file-input w-full rounded-xl border border-slate-200 bg-slate-50 p-1 text-[11px] text-slate-600">
                                </div>
                                <div>
                                    <label class="mb-0.5 block text-[11px] font-semibold text-slate-600">Deskripsi</label>
                                    <input type="text" name="description" value="{{ $menu->description }}" placeholder="Deskripsi" class="w-full rounded-xl border border-slate-200 px-2.5 py-1.5 text-xs">
                                </div>
                                <div class="flex flex-col gap-2.5 py-1.5 text-xs">
                                    <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" name="is_available" value="1" @checked($menu->is_available) class="rounded border-slate-300 text-[#E4572E] focus:ring-[#E4572E]"> Tersedia</label>
                                    <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" name="has_spicy_level" value="1" @checked($menu->has_spicy_level) class="rounded border-slate-300 text-[#E4572E] focus:ring-[#E4572E]"> Punya Level Pedas</label>
                                    <label class="flex cursor-pointer items-center gap-2"><input type="checkbox" name="has_temperature_option" value="1" @checked($menu->has_temperature_option) class="rounded border-slate-300 text-[#E4572E] focus:ring-[#E4572E]"> Opsi Hot / Ice</label>
                                </div>
                                <button type="submit" class="w-full rounded-xl bg-[#2A9D8F] py-2 text-xs font-bold text-white hover:opacity-90">Simpan</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
