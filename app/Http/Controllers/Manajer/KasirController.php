<?php

namespace App\Http\Controllers\Manajer;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class KasirController extends Controller
{
    public function index(): View
    {
        $kasirs = User::where('role', Role::Kasir)->orderBy('name')->get();

        return view('manajer.kasir', compact('kasirs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => Role::Kasir,
        ]);

        return back()->with('success', 'Akun kasir berhasil ditambahkan.');
    }

    public function destroy(User $kasir): RedirectResponse
    {
        abort_unless($kasir->role === Role::Kasir, 403);
        $kasir->delete();

        return back()->with('success', 'Akun kasir berhasil dihapus.');
    }
}
