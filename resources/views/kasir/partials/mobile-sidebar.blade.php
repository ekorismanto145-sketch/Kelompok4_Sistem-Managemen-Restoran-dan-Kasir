<div class="md:hidden">
    <div class="flex items-center justify-between border-b border-slate-200 bg-white px-4 py-3">
        <div class="flex items-center gap-2">
            <span class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></span>
            <span class="rounded-full bg-teal-100 px-2.5 py-0.5 text-xs font-bold text-teal-800">Kasir</span>
        </div>
        <button type="button" @click="drawerOpen = true" aria-label="Menu Kasir"
                class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-[#1E2A47] hover:bg-slate-50">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div x-show="drawerOpen" x-cloak class="fixed inset-0 z-50 flex">
        <div @click="drawerOpen = false" class="fixed inset-0 bg-slate-900/50"></div>
        <aside class="relative z-10 flex w-64 flex-col bg-[#1E2A47] p-5 text-white shadow-xl">
            <div class="flex items-center justify-between">
                <span class="text-xl font-extrabold">Pesan<span class="text-[#E4572E]">Aja</span></span>
                <button type="button" @click="drawerOpen = false" class="rounded-lg p-1 text-slate-300 hover:bg-white/10">✕</button>
            </div>
            <nav class="mt-8 space-y-1 text-sm">
                <a href="{{ route('kasir.queue') }}" class="block rounded-xl px-3.5 py-2.5 {{ request()->routeIs('kasir.queue') ? 'bg-white/10 font-semibold' : 'text-slate-300 hover:bg-white/5' }}">Antrian Pesanan</a>
                <a href="{{ route('kasir.pos') }}" class="block rounded-xl px-3.5 py-2.5 {{ request()->routeIs('kasir.pos') ? 'bg-white/10 font-semibold' : 'text-slate-300 hover:bg-white/5' }}">Daftar Menu (POS)</a>
                <a href="{{ route('kasir.history') }}" class="block rounded-xl px-3.5 py-2.5 {{ request()->routeIs('kasir.history') ? 'bg-white/10 font-semibold' : 'text-slate-300 hover:bg-white/5' }}">Histori Transaksi</a>
                <form method="POST" action="{{ route('logout') }}" class="pt-4">
                    @csrf
                    <button type="submit" style="background-color: #dc2626; color: #ffffff;" class="w-full rounded-xl px-3.5 py-2.5 text-left font-semibold hover:opacity-90">Keluar</button>
                </form>
            </nav>
        </aside>
    </div>
</div>
