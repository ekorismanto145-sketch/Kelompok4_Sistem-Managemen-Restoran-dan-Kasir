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
            'customer_name' => ['required', 'string', 'max:255', 'regex:/^(?=.*[a-zA-Z])[a-zA-Z\s]+$/'],
            'type' => ['required', 'in:dine_in,take_away'],
            'table_number' => ['required_if:type,dine_in', 'nullable', 'integer', 'min:1', 'max:200'],
            'payment_method' => ['required', 'in:midtrans,cash'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.spicy_level' => ['nullable', 'integer', 'min:0', 'max:8'],
            'items.*.temperature' => ['nullable', 'in:hot,ice'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ], [
            'customer_name.required' => 'Nama pemesan wajib diisi.',
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

        $guestOrderIds = $request->session()->get('guest_order_ids', []);
        $guestOrderIds[] = $order->id;
        $request->session()->put('guest_order_ids', array_values(array_unique($guestOrderIds)));

        if ($order->payment_method === 'midtrans') {
            return redirect()->route('customer.payment.show', $order);
        }

        return redirect()
            ->route('customer.orders.show', $order)
            ->with('success', "Pesanan {$order->code} berhasil dibuat. Silakan bayar di kasir.");
    }
}
