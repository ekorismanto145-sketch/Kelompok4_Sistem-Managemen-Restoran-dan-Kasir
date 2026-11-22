<x-app-layout title="Profil Saya · PesanAja">
    <style>
        .avatar-input::file-selector-button {
            background-color: #e2e8f0;
            color: #1e293b;
            border: 0;
            border-radius: 0.5rem;
            padding: 0.5rem 0.875rem;
            margin-right: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease, color 0.15s ease;
        }
        .avatar-input::file-selector-button:hover {
            background-color: #475569;
            color: #ffffff;
        }
    </style>
    <div class="mx-auto max-w-lg px-4 py-8">
        @php
            $backUrl = match ($user->role) {
                \App\Enums\Role::Manajer => route('manajer.dashboard'),
                \App\Enums\Role::Kasir => route('kasir.queue'),
                default => route('customer.menu'),
            };
        @endphp
        <div class="mb-4 flex items-center justify-between">
            <h1 class="text-xl font-extrabold">Profil Saya ({{ $user->role->label() }})</h1>
            <a href="{{ $backUrl }}" class="text-sm font-semibold text-[#E4572E]">← Kembali</a>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                @if ($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}"
                         class="h-16 w-16 rounded-full object-cover border border-slate-200">
                @else
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#1E2A47] text-xl font-bold text-white">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <div class="flex-1">
                    <p class="font-bold">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $user->role->label() }} · {{ $user->email }}</p>
                    @if ($user->avatar)
                        <form method="POST" action="{{ route('profile.avatar.destroy') }}" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Hapus Foto</button>
                        </form>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="text-xs font-semibold text-slate-600">Foto Profil (opsional, maks 2MB)</label>
                    <input type="file" name="avatar" accept="image/*" required
                           class="avatar-input mt-1 block w-full rounded-xl border border-slate-200 bg-slate-50 p-1.5 text-xs text-slate-600">
                    @error('avatar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#E4572E] focus:outline-none">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#E4572E] focus:outline-none">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Role Akun</label>
                    <select name="role" class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm focus:border-[#E4572E] focus:outline-none">
                        @foreach (\App\Enums\Role::cases() as $r)
                            <option value="{{ $r->value }}" @selected(old('role', $user->role->value) === $r->value)>{{ $r->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full rounded-xl bg-[#E4572E] py-3 text-sm font-bold text-white hover:bg-[#d14922]">
                    Simpan Perubahan
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4 border-t border-slate-100 pt-4">
                @csrf
                <button type="submit" class="w-full rounded-xl border border-red-200 bg-red-50 py-2.5 text-sm font-bold text-red-600 hover:bg-red-100">
                    Keluar (Logout)
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
