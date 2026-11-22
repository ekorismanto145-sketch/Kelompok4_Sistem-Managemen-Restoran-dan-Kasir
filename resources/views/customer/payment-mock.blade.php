<x-app-layout title="Simulasi Pembayaran · PesanAja">
    <div x-data="{ channel: '{{ $payment->payment_type ?? 'qris' }}' }" class="mx-auto max-w-md px-4 py-8">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Pembayaran</span>
                <span class="text-xs font-semibold text-slate-400">{{ $order->code }}</span>
            </div>

            <div class="mt-4 text-center">
                <p class="text-xs text-slate-500">Total Pembayaran</p>
                <p class="mt-1 text-3xl font-extrabold text-[#E4572E]">
                    <x-price :amount="$order->total" />
                </p>
            </div>

            {{-- Pilih Metode Virtual --}}
            <div class="mt-6">
                <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Pilih Metode Pembayaran</label>
                <div class="mt-2 grid grid-cols-2 gap-2">
                    @foreach ($channels as $key => $label)
                        <button type="button"
                                @click="channel = '{{ $key }}'"
                                :class="channel === '{{ $key }}' ? 'border-[#E4572E] bg-orange-50 text-[#E4572E]' : 'border-slate-200 bg-white text-slate-700'"
                                class="rounded-xl border px-3 py-2.5 text-xs font-bold transition">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Tampilan Mock QRIS / VA --}}
            <div class="mt-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-center">
                <div x-show="channel === 'qris'">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode('QRIS-PESANAJA-'.$order->code.'-'.$order->total) }}"
                         alt="QRIS {{ $order->code }}"
                         class="mx-auto h-44 w-44 rounded-xl border-4 border-[#1E2A47] bg-white p-2">
                    <p class="mt-2 text-xs text-slate-500">Scan QRIS di atas untuk pembayaran {{ $order->code }}</p>
                </div>

                <div x-show="channel === 'va_bca'" x-cloak>
                    <p class="text-xs text-slate-500">Nomor Virtual Account BCA</p>
                    <p class="mt-1 text-xl font-extrabold tracking-wider text-[#1E2A47]">8800{{ str_pad((string) $order->id, 8, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div x-show="channel === 'gopay' || channel === 'ovo'" x-cloak>
                    <p class="text-xs text-slate-500">Pembayaran E-Wallet</p>
                    <p class="mt-1 text-sm font-bold text-[#1E2A47]" x-text="channel.toUpperCase() + ' · {{ $order->code }}'"></p>
                </div>
            </div>

            {{-- Tombol Simulasi --}}
            <form method="POST" action="{{ route('customer.payment.simulate', $order) }}" class="mt-6 space-y-2">
                @csrf
                <input type="hidden" name="channel" :value="channel">

                <button type="submit" name="action" value="pay"
                        class="w-full rounded-xl bg-[#2A9D8F] py-3 text-sm font-bold text-white hover:opacity-90">
                    Bayar
                </button>

                <button type="submit" name="action" value="cancel"
                        class="w-full rounded-xl border border-slate-200 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batalkan Pesanan
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
