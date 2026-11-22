<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'date' => ['nullable', 'date', 'before_or_equal:today'],
            'code' => ['nullable', 'string', 'max:50'],
        ], [
            'date.before_or_equal' => 'Tanggal filter tidak boleh lebih dari hari ini.',
        ]);

        $orders = Order::with('user', 'items')
            ->when($request->filled('date'), fn ($q) => $q->whereDate('created_at', $request->date('date')))
            ->when($request->filled('code'), fn ($q) => $q->where('code', 'like', '%'.$request->string('code').'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('manajer.orders', compact('orders'));
    }
}
