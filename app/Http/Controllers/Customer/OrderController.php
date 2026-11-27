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
        if ($request->user()) {
            $orders = $request->user()
                ->orders()
                ->with('items')
                ->latest()
                ->get();
        } else {
            $guestOrderIds = $request->session()->get('guest_order_ids', []);
            $orders = Order::whereIn('id', $guestOrderIds)
                ->with('items')
                ->latest()
                ->get();
        }

        return view('customer.orders', compact('orders'));
    }

    private function authorizeOrderAccess(Request $request, Order $order): void
    {
        $isOwner = $request->user() && $order->user_id === $request->user()->id;
        $isGuestOwner = in_array($order->id, $request->session()->get('guest_order_ids', []), true);
        abort_unless($isOwner || $isGuestOwner, 403);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorizeOrderAccess($request, $order);

        $order->load('items', 'payment');

        return view('customer.order-status', compact('order'));
    }

    public function status(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrderAccess($request, $order);

        return response()->json([
            'status' => $order->status->value,
            'status_label' => $order->status->label(),
            'queue_number' => $order->queue_number,
        ]);
    }

    public function destroy(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $this->authorizeOrderAccess($request, $order);
        abort_unless($order->status === \App\Enums\OrderStatus::Selesai, 422, 'Hanya pesanan yang sudah selesai yang dapat dihapus.');

        $order->delete();

        return back()->with('success', 'Riwayat pesanan berhasil dihapus.');
    }
}
