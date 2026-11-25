<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request, OrderService $orderService): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:dine_in,take_away'],
            'table_number' => ['nullable', 'integer', 'min:1', 'max:200'],
            'payment_method' => ['required', 'in:midtrans,cash'],
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
            $data['payment_method'],
        );

        if ($order->payment_method === 'midtrans') {
            return redirect()->route('customer.payment.show', $order);
        }

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', "Pesanan {$order->code} berhasil dibuat. Silakan bayar di kasir.");
    }
}
