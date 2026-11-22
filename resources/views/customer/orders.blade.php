<x-app-layout title="Riwayat Pesanan · PesanAja">
    <div class="mx-auto max-w-3xl px-4 py-6">
        <div class="mb-5 flex items-center justify-between">
            <h1 class="text-xl font-extrabold">Riwayat Pesanan</h1>
            <a href="{{ route('customer.menu') }}" class="text-sm font-semibold text-[#E4572E]">← Kembali ke Menu</a>
        </div>

        @if ($orders->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                Belum ada pesanan.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($orders as $order)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-[#E4572E]">
                        <a href="{{ route('customer.orders.show', $order) }}" class="block">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-bold">{{ $order->code }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <span class="text-xs font-bold {{ match ($order->status->value) {
                                    'menunggu_bayar' => 'text-amber-600',
                                    'diproses' => 'text-blue-600',
                                    'siap', 'selesai' => 'text-teal-600',
                                    default => 'text-slate-500',
                                } }}">{{ $order->status->label() }}</span>
                            </div>
                        </a>
                        <div class="mt-3 flex items-center justify-between text-sm">
                            <span class="text-slate-600">{{ $order->items->sum('quantity') }} item · <x-price :amount="$order->total" class="font-bold text-[#1E2A47]" /></span>
                            @if ($order->status === \App\Enums\OrderStatus::Selesai)
                                <form method="POST" action="{{ route('customer.orders.destroy', $order) }}" onsubmit="return confirm('Hapus riwayat pesanan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
