<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MockPaymentController extends Controller
{
    public function show(Request $request, Order $order, PaymentService $paymentService): View
    {
        $isOwner = $request->user() && $order->user_id === $request->user()->id;
        $isGuestOwner = in_array($order->id, $request->session()->get('guest_order_ids', []), true);
        abort_unless($isOwner || $isGuestOwner, 403);

        $payment = $order->payment ?? $paymentService->initMockPayment($order);
        $channels = PaymentService::CHANNELS;

        return view('customer.payment-mock', compact('order', 'payment', 'channels'));
    }

    public function simulate(
        Request $request,
        Order $order,
        PaymentService $paymentService,
        OrderService $orderService
    ): RedirectResponse {
        $isOwner = $request->user() && $order->user_id === $request->user()->id;
        $isGuestOwner = in_array($order->id, $request->session()->get('guest_order_ids', []), true);
        abort_unless($isOwner || $isGuestOwner, 403);

        $data = $request->validate([
            'channel' => ['required', 'in:'.implode(',', array_keys(PaymentService::CHANNELS))],
            'action' => ['required', 'in:pay,cancel'],
        ]);

        if ($data['action'] === 'cancel') {
            $paymentService->simulateCancel($order);

            return redirect()
                ->route('customer.menu')
                ->with('success', 'Pesanan dibatalkan.');
        }

        $paidOrder = $paymentService->simulateSuccess($order, $data['channel'], $orderService);

        return redirect()
            ->route('customer.orders.show', $paidOrder)
            ->with('success', "Simulasi pembayaran berhasil! Nomor antrian: {$paidOrder->queue_number}");
    }
}
