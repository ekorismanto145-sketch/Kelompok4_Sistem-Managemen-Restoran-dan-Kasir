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
            'customer_name' => ['required', 'string', 'max:255', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z\s]+$/'],
            'type' => ['required', 'in:dine_in,take_away'],
            'table_number' => ['required_if:type,dine_in', 'nullable', 'integer', 'min:1', 'max:200'],
            'payment_method' => ['required', 'in:cash,qris,midtrans'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.spicy_level' => ['nullable', 'integer', 'min:0', 'max:8'],
            'items.*.temperature' => ['nullable', 'in:hot,ice'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ], [
            'customer_name.required' => 'Nama pembeli wajib diisi.',
            'customer_name.regex' => 'Nama hanya boleh berisi huruf dan spasi.',
            'table_number.required_if' => 'Nomor meja wajib diisi untuk Dine In.',
        ]);

        $order = $orderService->checkout(
            $request->user(),
            $data['items'],
            $data['type'],
            $data['table_number'] ?? null,
            $data['payment_method'],
            $data['customer_name'],
        );

        $paidOrder = $orderService->markPaid($order);

        Payment::updateOrCreate(
            ['order_id' => $paidOrder->id],
            [
                'payment_type' => $data['payment_method'],
                'status' => 'settlement',
            ]
        );

        return redirect()
            ->route('kasir.receipt', $paidOrder)
            ->with('success', "Pesanan {$paidOrder->code} berhasil dibuat & dibayar. Antrian: {$paidOrder->queue_number}");
    }

    public function receipt(Order $order): View
    {
        $order->load('items', 'payment', 'user');

        return view('kasir.receipt', compact('order'));
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
