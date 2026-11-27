<x-app-layout title="Buat Pesanan Kasir · PesanAja">
    <div x-data="{
        drawerOpen: false,
        search: '',
        cart: [],
        sheetOpen: false,
        selectedMenu: null,
        spicyLevel: 0,
        temperature: 'ice',
        note: '',
        qty: 1,
        extraFee(lvl) {
            if (lvl > 6) return 2000;
            if (lvl > 4) return 1000;
            return 0;
        },
        openMenu(menu) {
            this.selectedMenu = menu;
            this.spicyLevel = 0;
            this.temperature = 'ice';
            this.note = '';
            this.qty = 1;
            this.sheetOpen = true;
        },
        addToCart() {
            const extra = this.selectedMenu.has_spicy_level ? this.extraFee(this.spicyLevel) : 0;
            this.cart.push({
                id: this.selectedMenu.id,
                name: this.selectedMenu.name,
                price: this.selectedMenu.price + extra,
                has_spicy_level: this.selectedMenu.has_spicy_level,
                spicy_level: this.selectedMenu.has_spicy_level ? this.spicyLevel : null,
                has_temperature_option: this.selectedMenu.has_temperature_option,
                temperature: this.selectedMenu.has_temperature_option ? this.temperature : null,
                note: this.note,
                qty: this.qty,
            });
            this.sheetOpen = false;
        },
        get totalPrice() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },
        formatRp(val) {
            return 'Rp' + Number(val).toLocaleString('id-ID');
        }
    }" class="min-h-screen md:flex">
        <aside class="hidden w-60 shrink-0 bg-[#1E2A47] p-5 text-white md:block">
            <div class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('kasir.queue') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Antrian Pesanan</a>
                <a href="{{ route('kasir.pos') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Daftar Menu (POS)</a>
                <a href="{{ route('kasir.history') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Histori Transaksi</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left font-semibold hover:opacity-90">Keluar</button>
                </form>
            </nav>
        </aside>

        @include('kasir.partials.mobile-sidebar')

        <main class="flex-1 min-w-0 p-4 md:p-6 lg:grid lg:grid-cols-12 lg:gap-6">
            <section class="lg:col-span-8">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h1 class="text-xl font-extrabold">Daftar Menu (Kasir)</h1>
                    <input type="search" x-model="search" placeholder="Cari menu..."
                           class="w-56 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm focus:border-[#E4572E] focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                    @foreach ($menus as $menu)
                        <div x-show="search.trim() === '' || @js(strtolower($menu->name)).includes(search.toLowerCase())"
                             class="flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-3 shadow-sm {{ !$menu->is_available ? 'opacity-60' : '' }}">
                            <div>
                                <div class="relative mb-2.5 aspect-square overflow-hidden rounded-xl bg-slate-100">
                                    @if ($menu->image_url)
                                        <img src="{{ $menu->image_url }}" alt="{{ $menu->name }}" referrerpolicy="no-referrer" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-xs font-medium text-slate-400">
                                            {{ $menu->category->name }}
                                        </div>
                                    @endif
                                    @if (!$menu->is_available)
                                        <span class="absolute inset-0 flex items-center justify-center bg-slate-900/50 text-xs font-bold tracking-wider text-white">HABIS</span>
                                    @endif
                                </div>
                                <h3 class="text-sm font-bold leading-snug">{{ $menu->name }}</h3>
                                @if ($menu->has_spicy_level)
                                    <p class="mt-0.5 text-xs text-slate-500">Level 0–8</p>
                                @elseif ($menu->description)
                                    <p class="mt-0.5 line-clamp-1 text-xs text-slate-500">{{ $menu->description }}</p>
                                @endif
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <x-price :amount="$menu->price" class="text-sm font-bold text-[#E4572E]" />
                                <button type="button" @disabled(!$menu->is_available) @click="openMenu({{ Js::from($menu) }})"
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#E4572E] text-lg font-bold text-white disabled:bg-slate-300">+</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <aside class="mt-6 lg:col-span-4 lg:mt-0">
                <div class="sticky top-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-bold">Pesanan Tunai</h2>
                    <template x-if="cart.length === 0">
                        <p class="mt-3 text-sm text-slate-500">Pilih menu untuk ditambahkan.</p>
                    </template>
                    <div x-show="cart.length > 0" class="mt-4 space-y-3">
                        <template x-for="(item, idx) in cart" :key="idx">
                            <div class="flex justify-between border-b border-slate-100 pb-2 text-sm">
                                <span><span x-text="item.qty"></span>x <span x-text="item.name"></span></span>
                                <span class="font-semibold" x-text="formatRp(item.price * item.qty)"></span>
                            </div>
                        </template>
                        <div class="space-y-1 border-t border-slate-100 pt-2 text-xs text-slate-500">
                            <div class="flex justify-between"><span>Subtotal</span><span x-text="formatRp(totalPrice)"></span></div>
                            <div class="flex justify-between"><span>Pajak (10%)</span><span x-text="formatRp(Math.round(totalPrice * 0.10))"></span></div>
                        </div>
                        <div class="flex justify-between pt-1 text-base font-bold">
                            <span>Total Pembayaran</span>
                            <span class="text-[#E4572E]" x-text="formatRp(totalPrice + Math.round(totalPrice * 0.10))"></span>
                        </div>
                        <form method="POST" action="{{ route('kasir.pos.store') }}" class="space-y-2.5" x-data="{ paymentMethod: 'cash' }">
                            @csrf
                            <input type="hidden" name="type" value="take_away">
                            <template x-for="(item, idx) in cart" :key="idx">
                                <div>
                                    <input type="hidden" :name="`items[${idx}][menu_id]`" :value="item.id">
                                    <input type="hidden" :name="`items[${idx}][quantity]`" :value="item.qty">
                                    <input type="hidden" :name="`items[${idx}][spicy_level]`" :value="item.spicy_level ?? 0">
                                    <input type="hidden" :name="`items[${idx}][temperature]`" :value="item.temperature ?? ''">
                                    <input type="hidden" :name="`items[${idx}][note]`" :value="item.note">
                                </div>
                            </template>
                            <input type="text" name="customer_name" required pattern="[A-Za-z\s]+" title="Nama hanya boleh berisi huruf dan spasi"
                                   oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '')"
                                   placeholder="Nama Pembeli"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-[#E4572E] focus:outline-none">
                            <select name="payment_method" x-model="paymentMethod" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs">
                                <option value="cash">Tunai (Cash)</option>
                                <option value="qris">QRIS</option>
                            </select>
                            <div x-show="paymentMethod === 'qris'" x-cloak class="rounded-xl border border-teal-200 bg-teal-50 p-3 text-center text-xs text-teal-800">
                                <p class="font-bold">Scan QRIS</p>
                                <div class="my-1.5 flex justify-center">
                                    <svg style="width: 56px; height: 56px;" class="rounded-lg bg-white p-1 shadow-sm" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="100" height="100" fill="white"/>
                                        <rect x="8" y="8" width="26" height="26" stroke="#1E2A47" stroke-width="4"/>
                                        <rect x="15" y="15" width="12" height="12" fill="#1E2A47"/>
                                        <rect x="66" y="8" width="26" height="26" stroke="#1E2A47" stroke-width="4"/>
                                        <rect x="73" y="15" width="12" height="12" fill="#1E2A47"/>
                                        <rect x="8" y="66" width="26" height="26" stroke="#1E2A47" stroke-width="4"/>
                                        <rect x="15" y="73" width="12" height="12" fill="#1E2A47"/>
                                        <rect x="42" y="10" width="6" height="6" fill="#1E2A47"/>
                                        <rect x="52" y="10" width="6" height="6" fill="#1E2A47"/>
                                        <rect x="42" y="22" width="16" height="6" fill="#1E2A47"/>
                                        <rect x="10" y="42" width="12" height="6" fill="#1E2A47"/>
                                        <rect x="28" y="42" width="10" height="6" fill="#1E2A47"/>
                                        <rect x="44" y="42" width="12" height="12" fill="#E4572E"/>
                                        <rect x="62" y="42" width="10" height="6" fill="#1E2A47"/>
                                        <rect x="78" y="42" width="14" height="6" fill="#1E2A47"/>
                                        <rect x="10" y="54" width="6" height="6" fill="#1E2A47"/>
                                        <rect x="22" y="54" width="16" height="6" fill="#1E2A47"/>
                                        <rect x="62" y="54" width="16" height="6" fill="#1E2A47"/>
                                        <rect x="84" y="54" width="8" height="6" fill="#1E2A47"/>
                                        <rect x="42" y="66" width="8" height="12" fill="#1E2A47"/>
                                        <rect x="56" y="66" width="14" height="6" fill="#1E2A47"/>
                                        <rect x="76" y="66" width="16" height="6" fill="#1E2A47"/>
                                        <rect x="56" y="78" width="6" height="14" fill="#1E2A47"/>
                                        <rect x="68" y="78" width="12" height="6" fill="#1E2A47"/>
                                        <rect x="86" y="78" width="6" height="14" fill="#1E2A47"/>
                                        <rect x="42" y="84" width="8" height="8" fill="#1E2A47"/>
                                        <rect x="68" y="88" width="12" height="4" fill="#1E2A47"/>
                                    </svg>
                                </div>
                            </div>
                            <button type="submit" class="w-full rounded-xl bg-[#2A9D8F] py-3 text-sm font-bold text-white hover:opacity-90">
                                Proses &amp; Cetak Struk
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        </main>

        {{-- Bottom Sheet Level Pedas --}}
        <div x-show="sheetOpen" x-cloak class="fixed inset-0 z-40 flex items-end justify-center bg-slate-900/50 sm:items-center">
            <div @click.outside="sheetOpen = false" class="w-full max-w-md rounded-t-2xl bg-white p-5 sm:rounded-2xl">
                <template x-if="selectedMenu">
                    <div>
                        <h3 class="text-lg font-bold" x-text="selectedMenu.name"></h3>
                        <div x-show="selectedMenu.has_temperature_option" class="mt-3">
                            <label class="text-xs font-bold text-slate-500">Pilih penyajian</label>
                            <div class="mt-1.5 grid grid-cols-2 gap-1.5">
                                <button type="button" @click="temperature = 'hot'"
                                        :class="temperature === 'hot' ? 'bg-[#E4572E] text-white' : 'bg-slate-50 text-slate-700'"
                                        class="rounded-lg border border-slate-200 py-1.5 text-xs font-bold">
                                    Hot
                                </button>
                                <button type="button" @click="temperature = 'ice'"
                                        :class="temperature === 'ice' ? 'bg-[#E4572E] text-white' : 'bg-slate-50 text-slate-700'"
                                        class="rounded-lg border border-slate-200 py-1.5 text-xs font-bold">
                                    Ice
                                </button>
                            </div>
                        </div>

                        <div x-show="selectedMenu.has_spicy_level" class="mt-3">
                            <label class="text-xs font-bold text-slate-500">Pilih level pedas</label>
                            <div class="mt-2 grid grid-cols-5 gap-2">
                                <template x-for="lvl in [0,1,2,3,4,5,6,7,8]" :key="lvl">
                                    <button type="button" @click="spicyLevel = lvl"
                                            :class="spicyLevel === lvl ? 'bg-[#E4572E] text-white' : 'bg-slate-50 text-slate-700'"
                                            class="h-10 rounded-xl border border-slate-200 text-sm font-bold" x-text="lvl"></button>
                                </template>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-3">
                            <button type="button" @click="if (qty > 1) qty--" class="h-10 w-10 rounded-xl border border-slate-200 font-bold">−</button>
                            <span class="font-bold" x-text="qty"></span>
                            <button type="button" @click="qty++" class="h-10 w-10 rounded-xl border border-slate-200 font-bold">+</button>
                            <button type="button" @click="addToCart()" class="flex-1 rounded-xl bg-[#E4572E] py-2.5 text-sm font-bold text-white">
                                Tambah · <span x-text="formatRp((selectedMenu.price + (selectedMenu.has_spicy_level ? extraFee(spicyLevel) : 0)) * qty)"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</x-app-layout>
