<x-app-layout title="Menu · PesanAja">
    <div x-data="{
        drawerOpen: false,
        search: '{{ request('search', '') }}',
        cart: [],
        sheetOpen: false,
        selectedMenu: null,
        spicyLevel: 0,
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
                note: this.note,
                qty: this.qty,
            });
            this.sheetOpen = false;
        },
        get totalItems() {
            return this.cart.reduce((sum, item) => sum + item.qty, 0);
        },
        get totalPrice() {
            return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },
        formatRp(val) {
            return 'Rp' + Number(val).toLocaleString('id-ID');
        }
    }" class="pb-24 lg:pb-8">
        {{-- Header --}}
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-3">
                <a href="{{ route('customer.menu') }}" class="text-xl font-extrabold tracking-tight">
                    Pesan<span class="text-[#E4572E]">Aja</span>
                </a>

                <form method="GET" action="{{ route('customer.menu') }}" class="min-w-0 flex-1 max-w-md">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="search" name="search" x-model="search"
                           placeholder="Cari menu..."
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2 text-sm focus:border-[#E4572E] focus:bg-white focus:outline-none">
                </form>

                @auth
                    <button type="button" @click="drawerOpen = true" aria-label="Menu"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-[#1E2A47] hover:bg-slate-50">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                @else
                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ route('login') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold hover:bg-slate-50">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-[#E4572E] px-3 py-2 text-sm font-semibold text-white hover:bg-[#d14922]">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Category Chips --}}
            <div class="mx-auto flex max-w-7xl gap-2 overflow-x-auto px-4 pb-3">
                <a href="{{ route('customer.menu', array_filter(['search' => request('search')])) }}"
                   class="shrink-0 rounded-full px-4 py-1.5 text-sm font-semibold transition {{ !request('category') ? 'bg-[#E4572E] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                    Semua
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('customer.menu', array_filter(['category' => $category->id, 'search' => request('search')])) }}"
                       class="shrink-0 rounded-full px-4 py-1.5 text-sm font-semibold transition {{ (int) request('category') === $category->id ? 'bg-[#E4572E] text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </header>

        {{-- Main Content + Desktop Sticky Cart --}}
        <main class="mx-auto max-w-7xl px-4 pt-5 lg:grid lg:grid-cols-12 lg:gap-6">
            <section class="lg:col-span-8">
                @if ($menus->isEmpty())
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-slate-500">
                        Menu tidak ditemukan.
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3 md:grid-cols-3 lg:grid-cols-4">
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
                                            <span class="absolute inset-0 flex items-center justify-center bg-slate-900/50 text-xs font-bold tracking-wider text-white">
                                                HABIS
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-sm font-bold leading-snug">{{ $menu->name }}</h3>
                                    @if ($menu->has_spicy_level)
                                        <p class="mt-0.5 text-xs text-slate-500">Level 0–8</p>
                                    @elseif ($menu->description)
                                        <p class="mt-0.5 line-clamp-1 text-xs text-slate-500">{{ $menu->description }}</p>
                                    @endif
                                </div>

                                <div class="mt-3 flex items-center justify-between gap-2">
                                    <x-price :amount="$menu->price" class="text-sm font-bold text-[#E4572E]" />
                                    <button type="button"
                                            @disabled(!$menu->is_available)
                                            @click="openMenu({{ Js::from($menu) }})"
                                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#E4572E] text-lg font-bold text-white transition hover:bg-[#d14922] disabled:cursor-not-allowed disabled:bg-slate-300">
                                        +
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            {{-- Desktop Cart Sidebar --}}
            <aside class="hidden lg:col-span-4 lg:block">
                <div class="sticky top-28 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-bold">Keranjang</h2>
                    <template x-if="cart.length === 0">
                        <p class="mt-4 text-sm text-slate-500">Belum ada item dipilih.</p>
                    </template>
                    <div class="mt-4 space-y-3" x-show="cart.length > 0">
                        <template x-for="(item, idx) in cart" :key="idx">
                            <div class="flex items-start justify-between gap-2 border-b border-slate-100 pb-2 text-sm">
                                <div>
                                    <p class="font-semibold" x-text="`${item.qty}x ${item.name}`"></p>
                                    <p class="text-xs text-slate-500" x-show="item.spicy_level !== null" x-text="`Level ${item.spicy_level}`"></p>
                                    <p class="text-xs text-slate-400" x-show="item.note" x-text="item.note"></p>
                                </div>
                                <span class="font-semibold" x-text="formatRp(item.price * item.qty)"></span>
                            </div>
                        </template>
                        <div class="space-y-1 border-t border-slate-100 pt-2 text-xs text-slate-500">
                            <div class="flex justify-between"><span>Subtotal</span><span x-text="formatRp(totalPrice)"></span></div>
                            <div class="flex justify-between"><span>Pajak (10%)</span><span x-text="formatRp(Math.round(totalPrice * 0.10))"></span></div>
                        </div>
                        <div class="flex items-center justify-between pt-1 text-base font-bold">
                            <span>Total Pembayaran</span>
                            <span class="text-[#E4572E]" x-text="formatRp(totalPrice + Math.round(totalPrice * 0.10))"></span>
                        </div>
                        <form method="POST" action="{{ route('customer.checkout') }}" class="mt-3 space-y-2.5" x-data="{ type: 'dine_in', paymentMethod: 'midtrans' }">
                            @csrf
                            <template x-for="(item, idx) in cart" :key="idx">
                                <div>
                                    <input type="hidden" :name="`items[${idx}][menu_id]`" :value="item.id">
                                    <input type="hidden" :name="`items[${idx}][quantity]`" :value="item.qty">
                                    <input type="hidden" :name="`items[${idx}][spicy_level]`" :value="item.spicy_level ?? 0">
                                    <input type="hidden" :name="`items[${idx}][note]`" :value="item.note">
                                </div>
                            </template>

                            <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
                                <button type="button" @click="type = 'dine_in'" :class="type === 'dine_in' ? 'bg-[#E4572E] text-white' : 'bg-slate-100 text-slate-700'" class="rounded-lg py-2">Dine-in</button>
                                <button type="button" @click="type = 'take_away'" :class="type === 'take_away' ? 'bg-[#E4572E] text-white' : 'bg-slate-100 text-slate-700'" class="rounded-lg py-2">Take-away</button>
                            </div>
                            <input type="hidden" name="type" :value="type">

                            <input x-show="type === 'dine_in' && paymentMethod !== 'cash'" type="number" name="table_number" min="1" max="200" inputmode="numeric" placeholder="Nomor Meja (1–200)" :required="type === 'dine_in' && paymentMethod !== 'cash'"
                                   class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs focus:border-[#E4572E] focus:outline-none">

                            <select name="payment_method" x-model="paymentMethod" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs">
                                <option value="midtrans">Pembayaran Non-tunai</option>
                                <option value="cash">Pembayaran Tunai di Kasir</option>
                            </select>

                            <button type="submit" class="w-full rounded-xl bg-[#E4572E] py-3 text-sm font-bold text-white hover:bg-[#d14922]">
                                Checkout
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        </main>

        {{-- Mobile Bottom Cart Bar --}}
        <div x-show="totalItems > 0" x-cloak
             class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-[#1E2A47] p-3 text-white lg:hidden">
            <div class="mx-auto flex max-w-7xl items-center justify-between">
                <div class="text-sm">
                    <span class="font-bold" x-text="`${totalItems} item`"></span>
                    <span class="mx-1">·</span>
                    <span class="font-bold" x-text="formatRp(totalPrice)"></span>
                </div>
                <button type="button" class="rounded-xl bg-[#E4572E] px-5 py-2.5 text-sm font-bold text-white">
                    Lihat
                </button>
            </div>
        </div>

        {{-- Bottom Sheet: Pilih Level Pedas & Catatan --}}
        <div x-show="sheetOpen" x-cloak class="fixed inset-0 z-40 flex items-end justify-center bg-slate-900/50 sm:items-center">
            <div @click.outside="sheetOpen = false" class="w-full max-w-md rounded-t-2xl bg-white p-5 sm:rounded-2xl">
                <template x-if="selectedMenu">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold" x-text="selectedMenu.name"></h3>
                                <p class="text-sm text-slate-500" x-text="selectedMenu.description"></p>
                            </div>
                            <span class="font-bold text-[#E4572E]" x-text="formatRp(selectedMenu.price)"></span>
                        </div>

                        <div x-show="selectedMenu.has_spicy_level" class="mt-4">
                            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Pilih level pedas</label>
                            <div class="mt-2 grid grid-cols-5 gap-2">
                                <template x-for="lvl in [0,1,2,3,4,5,6,7,8]" :key="lvl">
                                    <button type="button"
                                            @click="spicyLevel = lvl"
                                            :class="spicyLevel === lvl ? 'bg-[#E4572E] text-white border-[#E4572E]' : 'bg-slate-50 text-slate-700 border-slate-200'"
                                            class="h-11 rounded-xl border text-sm font-bold transition"
                                            x-text="lvl">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="mt-4">
                            <input type="text" x-model="note" placeholder="Catatan (opsional, mis. tanpa daun bawang)"
                                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#E4572E] focus:outline-none">
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <div class="flex shrink-0 items-center gap-2">
                                <button type="button" @click="if (qty > 1) qty--" class="h-11 w-11 rounded-xl border border-slate-200 font-bold">−</button>
                                <span class="w-8 text-center font-bold" x-text="qty"></span>
                                <button type="button" @click="qty++" class="h-11 w-11 rounded-xl border border-slate-200 font-bold">+</button>
                            </div>
                            <button type="button" @click="addToCart()"
                                    class="flex-1 rounded-xl bg-[#E4572E] py-3 text-sm font-bold text-white hover:bg-[#d14922]">
                                Tambah · <span x-text="formatRp((selectedMenu.price + (selectedMenu.has_spicy_level ? extraFee(spicyLevel) : 0)) * qty)"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        {{-- Customer Slide-over Sidebar --}}
        @auth
            <div x-show="drawerOpen" x-cloak class="fixed inset-0 z-50 flex justify-end bg-slate-900/50">
                <div @click.outside="drawerOpen = false" class="flex h-full w-64 flex-col justify-between bg-[#1E2A47] p-5 text-white shadow-xl">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></span>
                            <button type="button" @click="drawerOpen = false" class="text-slate-300 hover:text-white">✕</button>
                        </div>
                        <nav class="mt-8 space-y-1 text-sm">
                            <a href="{{ route('customer.menu') }}" class="block rounded-xl bg-white/10 px-3.5 py-2.5 font-semibold">Menu</a>
                            <a href="{{ route('customer.orders') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Riwayat Transaksi</a>
                            <a href="{{ route('profile.edit') }}" class="block rounded-xl px-3.5 py-2.5 text-slate-300 hover:bg-white/5">Profile</a>
                        </nav>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left text-sm font-semibold hover:opacity-90">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</x-app-layout>
