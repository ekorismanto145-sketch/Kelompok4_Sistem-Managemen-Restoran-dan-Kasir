<?php

namespace App\Http\Controllers\Kasir;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('name')->get();
        $menus = Menu::with('category')->orderBy('category_id')->orderBy('name')->get();

        return view('kasir.pos', compact('categories', 'menus'));
    }

    public function store(Request $request, OrderService $orderService): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:dine_in,take_away'],
            'table_number' => ['nullable', 'integer', 'min:1', 'max:200'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.spicy_level' => ['nullable', 'integer', 'min:0', 'max:8'],
            'items.*.temperature' => ['nullable', 'in:hot,ice'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $order = $orderService->checkout(
            $request->user(),
            $data['items'],
            $data['type'],
            $data['table_number'] ?? null,
            'cash',
        );

        $paidOrder = $orderService->markPaid($order);

        Payment::updateOrCreate(
            ['order_id' => $paidOrder->id],
            [
                'payment_type' => 'cash',
                'status' => 'settlement',
            ]
        );

        return redirect()
            ->route('kasir.queue')
            ->with('success', "Pesanan {$paidOrder->code} berhasil dibuat & dibayar tunai. Antrian: {$paidOrder->queue_number}");
    }

    public function history(Request $request): View
    {
        $request->validate([
            'date' => ['nullable', 'date', 'before_or_equal:today'],
        ], [
            'date.before_or_equal' => 'Tanggal filter tidak boleh lebih dari hari ini.',
        ]);

        $orders = Order::with('user', 'items')
            ->whereIn('status', [
                OrderStatus::Diproses->value,
                OrderStatus::Siap->value,
                OrderStatus::Selesai->value,
            ])
            ->when($request->filled('date'), fn ($q) => $q->whereDate('created_at', $request->date('date')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('kasir.history', compact('orders'));
    }
}
