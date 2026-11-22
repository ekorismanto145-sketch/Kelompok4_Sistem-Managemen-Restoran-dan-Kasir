<x-app-layout title="Dashboard Manajer · PesanAja">
    <div x-data="{ navOpen: false }" class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('manajer.dashboard') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Dashboard</a>
                <a href="{{ route('manajer.orders.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Pesanan</a>
                <a href="{{ route('manajer.menus.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Laporan</a>
                <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Profil</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left font-semibold hover:opacity-90">Keluar</button>
                </form>
            </nav>
        </aside>

        <div class="flex-1 min-w-0">
            <header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3 md:hidden">
                <span class="text-lg font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></span>
                <button @click="navOpen = !navOpen" class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm font-semibold">Menu</button>
            </header>

            <nav x-show="navOpen" x-cloak class="border-b border-slate-200 bg-[#1E2A47] px-4 py-3 text-sm text-white md:hidden">
                <a href="{{ route('manajer.dashboard') }}" class="block py-2 font-semibold">Dashboard</a>
                <a href="{{ route('manajer.orders.index') }}" class="block py-2 text-slate-300">Pesanan</a>
                <a href="{{ route('manajer.menus.index') }}" class="block py-2 text-slate-300">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block py-2 text-slate-300">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block py-2 text-slate-300">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block py-2 text-slate-300">Laporan</a>
            </nav>

            <main class="mx-auto max-w-6xl p-4 md:p-8">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h1 class="text-xl font-extrabold text-[#1E2A47]">Ringkasan hari ini</h1>
                    <span class="rounded-xl border border-slate-200 bg-white px-3.5 py-1.5 text-xs font-semibold text-slate-600 shadow-sm">
                        {{ now()->translatedFormat('d M Y') }}
                    </span>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div style="background-color: #fff5f2;" class="rounded-2xl border border-orange-100 p-5">
                        <p class="text-xs font-semibold text-slate-500">Omzet</p>
                        <p class="mt-1.5 text-2xl font-extrabold text-[#1E2A47]"><x-price :amount="$omzetToday" /></p>
                    </div>
                    <div style="background-color: #f0f7ff;" class="rounded-2xl border border-blue-100 p-5">
                        <p class="text-xs font-semibold text-slate-500">Pesanan</p>
                        <p class="mt-1.5 text-2xl font-extrabold text-[#1E2A47]">{{ $ordersToday }}</p>
                    </div>
                    <div style="background-color: #f0fdf9;" class="rounded-2xl border border-teal-100 p-5">
                        <p class="text-xs font-semibold text-slate-500">Rata-rata / pesanan</p>
                        <p class="mt-1.5 text-2xl font-extrabold text-[#1E2A47]"><x-price :amount="$avgOrder" /></p>
                    </div>
                </div>

                <style>
                    .mgr-bottom-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
                    @media (min-width: 1024px) {
                        .mgr-bottom-grid { grid-template-columns: 7fr 5fr; }
                    }
                </style>
                <div class="mgr-bottom-grid mt-6">
                    <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-sm font-bold text-[#1E2A47]">Pesanan per jam</h2>
                        @php $maxHourly = max(1, $hourlyOrders->max()); @endphp
                        <div class="mt-4 flex h-44 items-end gap-2 pt-4">
                            @foreach ($hourlyOrders as $hour => $count)
                                <div class="flex flex-1 flex-col items-center gap-1">
                                    <span class="text-[10px] font-semibold text-slate-500">{{ $count }}</span>
                                    <div class="w-full rounded-t-md {{ $count === $maxHourly && $count > 0 ? 'bg-[#E4572E]' : 'bg-[#3D7DD8]' }}"
                                         style="height: {{ max(10, (int) round(($count / $maxHourly) * 110)) }}px"></div>
                                    <span class="text-[10px] text-slate-400">{{ $hour }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-sm font-bold text-[#1E2A47]">Menu terlaris</h2>
                        <ul class="mt-4 space-y-2.5 text-sm">
                            @forelse ($topMenus as $idx => $item)
                                <li class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span>{{ $idx + 1 }}. {{ $item->menu_name }}</span>
                                    <span class="font-bold text-[#E4572E]">{{ $item->total_qty }}</span>
                                </li>
                            @empty
                                <li class="text-xs text-slate-400">Belum ada pesanan hari ini.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
