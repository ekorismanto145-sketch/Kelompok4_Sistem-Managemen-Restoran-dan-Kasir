<x-app-layout title="Struk Pembelian {{ $order->code }}">
    <div class="min-h-screen bg-slate-50 p-4 md:p-8">
    <div class="mx-auto max-w-md">
        <div class="mb-4 flex items-center justify-between print:hidden">
            <a href="{{ route('kasir.queue') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                ← Kembali ke Antrian
            </a>
            <div class="flex gap-2">
                <a href="{{ route('kasir.pos') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    POS Baru
                </a>
                <button type="button" onclick="window.print()" class="rounded-xl bg-[#E4572E] px-4 py-2 text-sm font-bold text-white hover:bg-[#d14922]">
                    Cetak Struk
                </button>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm print:border-0 print:shadow-none">
            <div class="border-b border-dashed border-slate-300 pb-4 text-center">
                <h1 class="text-lg font-extrabold text-[#1E2A47]">STRUK PEMBELIAN</h1>
                <p class="mt-1 text-xs font-mono text-slate-600">{{ $order->code }}</p>
            </div>

            <div class="space-y-1.5 border-b border-dashed border-slate-300 py-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-500">Tanggal</span>
                    <span class="font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pembeli</span>
                    <span class="font-semibold">{{ $order->customer_name ?? $order->user?->name ?? 'Pelanggan' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tipe</span>
                    <span class="font-medium">{{ $order->type === 'dine_in' ? 'Dine In (Meja ' . $order->table_number . ')' : 'Take Away' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Antrian</span>
                    <span class="font-bold text-[#E4572E]">{{ $order->queue_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Metode Bayar</span>
                    <span class="font-semibold uppercase">{{ $order->payment_method }}</span>
                </div>
            </div>

            <div class="space-y-2 border-b border-dashed border-slate-300 py-3 text-xs">
                @foreach ($order->items as $item)
                    <div class="flex justify-between gap-2">
                        <div>
                            <p class="font-semibold text-slate-800">{{ $item->menu_name }}</p>
                            <p class="text-[11px] text-slate-500">
                                {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                @if ($item->spicy_level) · Pedas {{ $item->spicy_level }} @endif
                                @if ($item->temperature) · {{ strtoupper($item->temperature) }} @endif
                            </p>
                        </div>
                        <span class="font-semibold text-slate-800">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            @php
                $subtotal = $order->items->sum(fn($i) => $i->price * $i->quantity);
                $tax = $order->total - $subtotal;
            @endphp

            <div class="space-y-1.5 pt-3 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Pajak (10%)</span>
                    <span>Rp {{ number_format($tax, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-slate-200 pt-2 text-sm font-extrabold text-[#1E2A47]">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <p class="mt-5 text-center text-[11px] text-slate-400">Terima kasih atas kunjungan Anda!</p>
        </div>
    </div>
    </div>
</x-app-layout>
