<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    public const CHANNELS = [
        'qris' => 'QRIS',
        'va_bca' => 'Virtual Account BCA',
        'gopay' => 'GoPay',
        'ovo' => 'OVO',
    ];

    public function initMockPayment(Order $order, string $channel = 'qris'): Payment
    {
        return Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'midtrans_order_id' => 'MOCK-'.$order->code,
                'payment_type' => $channel,
                'status' => 'pending',
                'raw_response' => [
                    'mode' => 'virtual_sandbox',
                    'channel' => $channel,
                    'va_number' => $channel === 'va_bca' ? '8800'.str_pad((string) $order->id, 8, '0', STR_PAD_LEFT) : null,
                ],
            ]
        );
    }

    public function simulateSuccess(Order $order, string $channel, OrderService $orderService): Order
    {
        $paidOrder = $orderService->markPaid($order);

        Payment::updateOrCreate(
            ['order_id' => $paidOrder->id],
            [
                'midtrans_order_id' => 'MOCK-'.$paidOrder->code,
                'payment_type' => $channel,
                'status' => 'settlement',
                'raw_response' => [
                    'mode' => 'virtual_sandbox',
                    'channel' => $channel,
                    'simulated_at' => now()->toIso8601String(),
                ],
            ]
        );

        return $paidOrder;
    }

    public function simulateCancel(Order $order): void
    {
        if ($order->status === OrderStatus::MenungguBayar) {
            $order->delete();
        }
    }
}
