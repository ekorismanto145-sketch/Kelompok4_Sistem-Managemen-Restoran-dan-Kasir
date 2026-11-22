<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function cash(Order $order, OrderService $orderService): RedirectResponse
    {
        $paidOrder = $orderService->markPaid($order);

        Payment::updateOrCreate(
            ['order_id' => $paidOrder->id],
            [
                'payment_type' => 'cash',
                'status' => 'settlement',
            ]
        );

        return back()->with('success', "Pembayaran tunai {$paidOrder->code} diterima. Antrian: {$paidOrder->queue_number}");
    }
}
