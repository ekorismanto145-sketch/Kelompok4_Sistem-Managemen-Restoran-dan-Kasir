<x-app-layout title="Antrian Kasir · PesanAja">
    <div x-data="{ tab: 'menunggu' }" class="min-h-screen bg-slate-50 md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('kasir.queue') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Antrian Pesanan</a>
                <a href="{{ route('kasir.pos') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Daftar Menu (POS)</a>
                <a href="{{ route('kasir.history') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Histori Transaksi</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left font-semibold hover:opacity-90">Keluar</button>
                </form>
            </nav>
        </aside>

        <div class="flex-1 min-w-0">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></span>
                    <span class="rounded-full bg-teal-100 px-2.5 py-0.5 text-xs font-bold text-teal-800">Kasir</span>
                </div>
                <div class="flex items-center gap-3">
                    <span x-data="{ now: '' }" x-init="const upd = () => now = new Date().toLocaleString('id-ID', { weekday:'short', day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' }); upd(); setInterval(upd, 1000)"
                          x-text="now" class="hidden text-xs font-medium text-slate-500 sm:inline"></span>
                    <a href="{{ route('profile.edit') }}" title="Profil"
                       class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-[#1E2A47] text-xs font-bold text-white hover:opacity-90">
                        @if (auth()->user()->avatar)
                            <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="Profil" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        @endif
                    </a>
                </div>
            </div>
        </header>

        {{-- Mobile Tabs --}}
        <div class="flex border-b border-slate-200 bg-white md:hidden">
            <button @click="tab = 'menunggu'" :class="tab === 'menunggu' ? 'border-[#E4572E] text-[#E4572E]' : 'border-transparent text-slate-500'"
                    class="flex-1 border-b-2 py-3 text-xs font-bold">Menunggu ({{ $menungguBayar->count() }})</button>
            <button @click="tab = 'diproses'" :class="tab === 'diproses' ? 'border-[#3D7DD8] text-[#3D7DD8]' : 'border-transparent text-slate-500'"
                    class="flex-1 border-b-2 py-3 text-xs font-bold">Diproses ({{ $diproses->count() }})</button>
            <button @click="tab = 'siap'" :class="tab === 'siap' ? 'border-[#2A9D8F] text-[#2A9D8F]' : 'border-transparent text-slate-500'"
                    class="flex-1 border-b-2 py-3 text-xs font-bold">Siap ({{ $siap->count() }})</button>
        </div>

        <main class="mx-auto grid max-w-7xl gap-4 p-4 md:grid-cols-3">
            {{-- Kolom Menunggu Bayar --}}
            <section :class="tab === 'menunggu' ? 'block' : 'hidden md:block'" class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="font-bold">Menunggu Bayar</h2>
                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">{{ $menungguBayar->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($menungguBayar as $order)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="flex justify-between text-sm font-bold">
                                <span>{{ $order->code }}</span>
                                <x-price :amount="$order->total" />
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $order->user->name }} · {{ $order->type === 'dine_in' ? 'Meja '.$order->table_number : 'Take-away' }}</p>
                            <form method="POST" action="{{ route('kasir.orders.cash', $order) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="w-full rounded-xl bg-[#F2A541] py-2.5 text-xs font-bold text-white hover:opacity-90">
                                    Terima Pembayaran
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="py-6 text-center text-xs text-slate-400">Belum ada pesanan.</p>
                    @endforelse
                </div>
            </section>

            {{-- Kolom Diproses --}}
            <section :class="tab === 'diproses' ? 'block' : 'hidden md:block'" class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="font-bold">Diproses</h2>
                    <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-800">{{ $diproses->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($diproses as $order)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="flex justify-between text-sm font-bold">
                                <span>{{ $order->queue_number ?? $order->code }}</span>
                                <span class="text-xs text-slate-500">{{ $order->type === 'dine_in' ? 'Meja '.$order->table_number : 'Take-away' }}</span>
                            </div>
                            <ul class="mt-2 space-y-0.5 text-xs text-slate-600">
                                @foreach ($order->items as $item)
                                    <li>{{ $item->quantity }}x {{ $item->menu_name }} @if(!is_null($item->spicy_level)) Lv{{ $item->spicy_level }} @endif @if($item->temperature) ({{ ucfirst($item->temperature) }}) @endif</li>
                                @endforeach
                            </ul>
                            <form method="POST" action="{{ route('kasir.orders.status', $order) }}" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="siap">
                                <button type="submit" class="w-full rounded-xl bg-[#3D7DD8] py-2.5 text-xs font-bold text-white hover:opacity-90">
                                    Tandai Siap →
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="py-6 text-center text-xs text-slate-400">Belum ada pesanan.</p>
                    @endforelse
                </div>
            </section>

            {{-- Kolom Siap --}}
            <section :class="tab === 'siap' ? 'block' : 'hidden md:block'" class="rounded-2xl border border-slate-200 bg-white p-4">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="font-bold">Siap</h2>
                    <span class="rounded-full bg-teal-100 px-2.5 py-0.5 text-xs font-bold text-teal-800">{{ $siap->count() }}</span>
                </div>
                <div class="space-y-3">
                    @forelse ($siap as $order)
                        <div class="rounded-xl border border-slate-200 p-3">
                            <div class="flex justify-between text-sm font-bold">
                                <span>{{ $order->queue_number ?? $order->code }}</span>
                                <span class="text-xs text-slate-500">{{ $order->type === 'dine_in' ? 'Meja '.$order->table_number : 'Take-away' }}</span>
                            </div>
                            <form method="POST" action="{{ route('kasir.orders.status', $order) }}" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="selesai">
                                <button type="submit" class="w-full rounded-xl bg-[#2A9D8F] py-2.5 text-xs font-bold text-white hover:opacity-90">
                                    Selesai ✓
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="py-6 text-center text-xs text-slate-400">Belum ada pesanan.</p>
                    @endforelse
                </div>
            </section>
        </main>
        </div>
    </div>
</x-app-layout>
