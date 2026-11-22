<x-app-layout title="Status Pesanan · PesanAja">
    <div x-data="{
        status: '{{ $order->status->value }}',
        queueNumber: '{{ $order->queue_number ?? '-' }}',
        init() {
            setInterval(async () => {
                const res = await fetch('{{ route('customer.orders.status', $order) }}');
                if (res.ok) {
                    const data = await res.json();
                    this.status = data.status;
                    this.queueNumber = data.queue_number ?? '-';
                }
            }, 5000);
        }
    }" class="mx-auto max-w-lg px-4 py-6">
        <div class="mb-4 flex items-center justify-between">
            <a href="{{ route('customer.orders') }}" class="text-sm font-semibold text-slate-600">← Riwayat</a>
            <span class="text-xs text-slate-400">Diperbarui otomatis tiap 5 detik</span>
        </div>

        <div class="rounded-2xl bg-[#E4572E] p-6 text-center text-white shadow-sm">
            @if ($order->payment_method === 'cash' && $order->status->value === 'menunggu_bayar')
                <p class="text-xs font-semibold uppercase tracking-wider opacity-90">Kode Pembelian (Tunjukkan ke Kasir)</p>
                <p class="mt-1 text-3xl font-extrabold tracking-wider">{{ $order->code }}</p>
                <p class="mt-2 text-xs opacity-90">Silakan bayar tunai sebesar <x-price :amount="$order->total" class="font-bold" /> di kasir</p>
            @else
                <p class="text-xs font-semibold uppercase tracking-wider opacity-90">Nomor Antrian Kamu</p>
                <p class="mt-1 text-4xl font-extrabold" x-text="queueNumber"></p>
                <p class="mt-1 text-xs opacity-90">
                    {{ $order->type === 'dine_in' ? ($order->table_number ? 'Dine-in · Meja '.$order->table_number : 'Dine-in') : 'Take-away' }}
                </p>
            @endif
        </div>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-500">Status Pesanan</h2>
            <ol class="space-y-3 text-sm">
                @foreach ([
                    'menunggu_bayar' => 'Menunggu Pembayaran',
                    'diproses' => 'Sedang Diproses',
                    'siap' => 'Siap Diambil',
                    'selesai' => 'Selesai',
                ] as $key => $label)
                    <li class="flex items-center gap-3">
                        <span :class="status === '{{ $key }}' ? 'bg-[#2A9D8F] ring-4 ring-teal-100' : 'bg-slate-200'"
                              class="h-3 w-3 rounded-full"></span>
                        <span :class="status === '{{ $key }}' ? 'font-bold text-[#1E2A47]' : 'text-slate-500'">
                            {{ $label }}
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-500">Detail Pesanan</h2>
            <div class="space-y-2 text-sm">
                @foreach ($order->items as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->quantity }}x {{ $item->menu_name }} @if(!is_null($item->spicy_level)) (Lv{{ $item->spicy_level }}) @endif</span>
                        <x-price :amount="$item->price * $item->quantity" class="font-semibold" />
                    </div>
                @endforeach
                <div class="flex justify-between border-t border-slate-100 pt-2 font-bold">
                    <span>Total</span>
                    <x-price :amount="$order->total" class="text-[#E4572E]" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
