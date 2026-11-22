<?php

namespace App\Http\Controllers\Kasir;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QueueController extends Controller
{
    public function index(): View
    {
        $todayOrders = Order::with('items', 'user')
            ->whereDate('created_at', today())
            ->whereIn('status', [
                OrderStatus::MenungguBayar->value,
                OrderStatus::Diproses->value,
                OrderStatus::Siap->value,
            ])
            ->orderBy('created_at')
            ->get();

        return view('kasir.queue', [
            'menungguBayar' => $todayOrders->where('status', OrderStatus::MenungguBayar),
            'diproses' => $todayOrders->where('status', OrderStatus::Diproses),
            'siap' => $todayOrders->where('status', OrderStatus::Siap),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $nextStatus = OrderStatus::from($request->string('status')->toString());

        abort_unless($order->status->canTransitionTo($nextStatus), 422, 'Transisi status tidak valid.');

        $order->update(['status' => $nextStatus]);

        return back()->with('success', "Status pesanan {$order->code} diperbarui ke {$nextStatus->label()}.");
    }
}
