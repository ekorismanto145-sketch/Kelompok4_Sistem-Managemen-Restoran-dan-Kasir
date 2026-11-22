<x-app-layout title="Daftar Pesanan · PesanAja">
    <div class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('manajer.dashboard') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Dashboard</a>
                <a href="{{ route('manajer.orders.index') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Pesanan</a>
                <a href="{{ route('manajer.menus.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Laporan</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left font-semibold hover:opacity-90">Keluar</button>
                </form>
            </nav>
        </aside>

        <main x-data="{ codeSearch: @js(request('code', '')) }" class="flex-1 p-4 md:p-8">
            <div class="mx-auto max-w-5xl">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h1 class="text-xl font-extrabold">Daftar Pesanan</h1>
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <input type="text" name="code" x-model="codeSearch" placeholder="Cari kode pesanan..."
                               class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs">
                        <input type="date" name="date" value="{{ request('date') }}" max="{{ today()->format('Y-m-d') }}"
                               class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs">
                        <button type="submit" class="rounded-xl bg-[#E4572E] px-4 py-2 text-xs font-bold text-white">Filter</button>
                        @if (request()->anyFilled(['date', 'code']))
                            <a href="{{ route('manajer.orders.index') }}" class="text-xs font-semibold text-slate-500">Reset</a>
                        @endif
                    </form>
                </div>
                @error('date') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                <div class="mt-5 space-y-3">
                    @forelse ($orders as $order)
                        <div x-show="codeSearch.trim() === '' || @js(strtolower($order->code)).includes(codeSearch.toLowerCase())"
                             class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-4">
                            <div>
                                <p class="font-bold">{{ $order->code }} <span class="text-xs font-normal text-slate-400">· {{ $order->user->name }}</span></p>
                                <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y H:i') }} · {{ $order->items->sum('quantity') }} item</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-price :amount="$order->total" class="font-bold" />
                                <x-status-badge :status="$order->status" />
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                            Belum ada pesanan.
                        </div>
                    @endforelse
                </div>
                <div class="mt-4">{{ $orders->links() }}</div>
            </div>
        </main>
    </div>
</x-app-layout>
