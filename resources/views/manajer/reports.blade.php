<x-app-layout title="Laporan Penjualan · PesanAja">
    <div class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('manajer.dashboard') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Dashboard</a>
                <a href="{{ route('manajer.menus.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Laporan</a>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8">
            <div class="mx-auto max-w-5xl">
                <h1 class="text-xl font-extrabold">Laporan Penjualan</h1>

                <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500">Dari</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500">Sampai</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="rounded-xl bg-[#E4572E] px-5 py-2.5 text-sm font-bold text-white">Filter</button>
                </form>

                <div class="mt-5 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <p class="text-xs font-semibold text-slate-500">Total Omzet</p>
                        <p class="mt-1 text-2xl font-extrabold text-[#1E2A47]"><x-price :amount="$totalOmzet" /></p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <p class="text-xs font-semibold text-slate-500">Pembayaran Online</p>
                        <p class="mt-1 text-2xl font-extrabold text-[#3D7DD8]"><x-price :amount="$byMethod->get('midtrans', 0)" /></p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <p class="text-xs font-semibold text-slate-500">Pembayaran Tunai</p>
                        <p class="mt-1 text-2xl font-extrabold text-[#2A9D8F]"><x-price :amount="$byMethod->get('cash', 0)" /></p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">
                    <h2 class="font-bold">5 Menu Terlaris</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        @forelse ($topMenus as $idx => $item)
                            <li class="flex justify-between border-b border-slate-100 pb-2">
                                <span>{{ $idx + 1 }}. {{ $item->menu_name }}</span>
                                <span class="font-bold">{{ $item->total_qty }} terjual</span>
                            </li>
                        @empty
                            <li class="text-slate-400">Belum ada data penjualan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
