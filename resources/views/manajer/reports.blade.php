<x-app-layout title="Laporan Penjualan · PesanAja">
    <div class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block print:hidden">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('manajer.dashboard') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Dashboard</a>
                <a href="{{ route('manajer.menus.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Menu</a>
                <a href="{{ route('manajer.categories.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Kategori</a>
                <a href="{{ route('manajer.kasir.index') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Akun Kasir</a>
                <a href="{{ route('manajer.reports') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Laporan</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left font-semibold hover:opacity-90">Keluar</button>
                </form>
            </nav>
        </aside>

        <main class="flex-1 p-4 md:p-8 print:p-0">
            <div class="mx-auto max-w-5xl">
                {{-- Kop Laporan (tampil saat cetak PDF) --}}
                <div class="hidden border-b-2 border-[#1E2A47] pb-4 mb-6 print:block">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#E4572E] text-white">
                                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6c-4.418 0-8 3.134-8 7h16c0-3.866-3.582-7-8-7zm0-2v2m-9 9h18m-2 0a2 2 0 01-2 2H7a2 2 0 01-2-2" />
                                </svg>
                            </span>
                            <div>
                                <div class="text-2xl font-extrabold text-[#1E2A47]">Pesan<span class="text-[#E4572E]">Aja</span></div>
                                <p class="text-xs text-slate-500">Sistem Pemesanan &amp; Kasir Restoran Digital · {{ config('app.url') }}</p>
                            </div>
                        </div>
                        <div class="text-right text-xs text-slate-600">
                            <p class="font-bold text-slate-800">LAPORAN PENJUALAN</p>
                            <p>Periode: {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}</p>
                            <p>Dicetak: {{ now()->format('d M Y, H:i') }} WIB</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
                    <h1 class="text-xl font-extrabold">Laporan Penjualan</h1>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-bold text-[#1E2A47] hover:bg-slate-50">
                            Cetak PDF
                        </button>
                        <a href="{{ route('manajer.reports.excel', request()->query()) }}" class="rounded-xl bg-[#2A9D8F] px-4 py-2 text-sm font-bold text-white hover:opacity-90">
                            Export Excel
                        </a>
                    </div>
                </div>

                <form method="GET" class="mt-4 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 print:hidden">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500">Dari</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500">Sampai</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" max="{{ today()->format('Y-m-d') }}" class="mt-1 rounded-xl border border-slate-200 px-3 py-2 text-sm">
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

                <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="font-bold">
                            Daftar Transaksi
                            <span class="text-sm font-normal text-slate-500">({{ $totalOrders }} total)</span>
                        </h2>

                        @if ($totalSlides > 1)
                            <form method="GET" class="flex items-center gap-2 print:hidden">
                                <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                                <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                                <label class="text-xs font-semibold text-slate-500">Pilih Slide:</label>
                                <select name="slide" onchange="this.form.submit()" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold">
                                    @for ($s = 1; $s <= $totalSlides; $s++)
                                        <option value="{{ $s }}" @selected($s === $currentSlide)>Slide {{ $s }}</option>
                                    @endfor
                                </select>
                            </form>
                        @endif
                    </div>
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-slate-200 text-xs uppercase text-slate-500">
                                    <th class="py-2 pr-3">No</th>
                                    <th class="py-2 pr-3">Kode</th>
                                    <th class="py-2 pr-3">Tanggal</th>
                                    <th class="py-2 pr-3">Pelanggan</th>
                                    <th class="py-2 pr-3">Metode</th>
                                    <th class="py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pagedOrders as $idx => $order)
                                    <tr class="border-b border-slate-100">
                                        <td class="py-2 pr-3 text-slate-500">{{ (($currentSlide - 1) * $perSlide) + $idx + 1 }}</td>
                                        <td class="py-2 pr-3 font-semibold">{{ $order->order_code ?? ('#' . $order->id) }}</td>
                                        <td class="py-2 pr-3 text-slate-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-2 pr-3">{{ $order->user?->name ?? 'Tamu' }}</td>
                                        <td class="py-2 pr-3">{{ $order->payment_method === 'cash' ? 'Tunai' : 'Online' }}</td>
                                        <td class="py-2 text-right font-bold"><x-price :amount="$order->total" /></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-slate-400">Belum ada transaksi pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex justify-end border-t border-slate-100 pt-2 text-xs font-semibold text-slate-500">
                        {{ $currentSlide }}/{{ $totalSlides }}
                    </div>
                </div>
            </div>
        </main>
    </div>
</x-app-layout>
