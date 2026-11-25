<x-app-layout title="Histori Transaksi Kasir · PesanAja">
    <div class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('kasir.queue') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Antrian Pesanan</a>
                <a href="{{ route('kasir.pos') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Daftar Menu (POS)</a>
                <a href="{{ route('kasir.history') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Histori Transaksi</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left font-semibold hover:opacity-90">Keluar</button>
                </form>
            </nav>
        </aside>

        <main class="flex-1 min-w-0 p-4 md:p-8">
            <div class="mx-auto max-w-5xl">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <h1 class="text-xl font-extrabold">Histori Transaksi</h1>
                    <form method="GET" class="flex items-center gap-2">
                        <input type="date" name="date" value="{{ request('date') }}" max="{{ today()->format('Y-m-d') }}"
                               class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs">
                        <button type="submit" class="rounded-xl bg-[#E4572E] px-4 py-2 text-xs font-bold text-white">Filter</button>
                        @if (request('date'))
                            <a href="{{ route('kasir.history') }}" class="text-xs font-semibold text-slate-500">Reset</a>
                        @endif
                    </form>
                </div>
                @error('date') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror

                <div class="mt-5 space-y-3">
                    @forelse ($orders as $order)
                        <div x-data="{ open: false }" class="rounded-2xl border border-slate-200 bg-white p-4">
                            <button type="button" @click="open = !open" class="flex w-full flex-wrap items-center justify-between gap-3 text-left">
                                <div>
                                    <p class="font-bold">{{ $order->queue_number ?? $order->code }} <span class="text-xs font-normal text-slate-400">· {{ $order->code }}</span></p>
                                    <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y, H:i') }} · {{ strtoupper($order->payment_method) }} · {{ $order->items->sum('quantity') }} item</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-price :amount="$order->total" class="font-bold text-[#1E2A47]" />
                                    <x-status-badge :status="$order->status" />
                                </div>
                            </button>
                            <div x-show="open" x-cloak class="mt-3 border-t border-slate-100 pt-3 text-xs text-slate-600 space-y-1">
                                @foreach ($order->items as $item)
                                    <div class="flex justify-between">
                                        <span>{{ $item->quantity }}x {{ $item->menu_name }} @if(!is_null($item->spicy_level)) (Lv{{ $item->spicy_level }}) @endif @if($item->temperature) ({{ ucfirst($item->temperature) }}) @endif</span>
                                        <x-price :amount="$item->price * $item->quantity" />
                                    </div>
                                @endforeach
                                @php $sub = $order->items->sum(fn($i) => $i->price * $i->quantity); @endphp
                                <div class="flex justify-between border-t border-slate-100 pt-1.5 font-semibold">
                                    <span>Pajak (10%)</span>
                                    <x-price :amount="$order->total - $sub" />
                                </div>
                                <div class="flex justify-between font-bold text-[#1E2A47]">
                                    <span>Total Pembayaran</span>
                                    <x-price :amount="$order->total" />
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">
                            Belum ada transaksi.
                        </div>
                    @endforelse
                </div>
                <div class="mt-4">{{ $orders->links() }}</div>
            </div>
        </main>
    </div>
</x-app-layout>
