<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $orders = $request->user()
            ->orders()
            ->with('items')
            ->latest()
            ->get();

        return view('customer.orders', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items', 'payment');

        return view('customer.order-status', compact('order'));
    }

    public function status(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return response()->json([
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'queue_number' => $order->queue_number,
        ]);
    }

    public function destroy(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->status === \App\Enums\OrderStatus::Selesai, 422, 'Hanya pesanan yang sudah selesai yang dapat dihapus.');

        $order->delete();

        return back()->with('success', 'Riwayat pesanan berhasil dihapus.');
    }
}
