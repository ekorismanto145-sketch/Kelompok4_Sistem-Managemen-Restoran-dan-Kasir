<?php

namespace App\Http\Controllers\Manajer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $paidStatuses = [
            OrderStatus::Diproses->value,
            OrderStatus::Siap->value,
            OrderStatus::Selesai->value,
        ];

        $paidToday = Order::whereDate('created_at', today())
            ->whereIn('status', $paidStatuses);

        $omzetToday = (clone $paidToday)->sum('total');
        $ordersToday = (clone $paidToday)->count();
        $avgOrder = $ordersToday > 0 ? (int) round($omzetToday / $ordersToday) : 0;

        $hourlyRaw = (clone $paidToday)
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy('hour')
            ->pluck('total', 'hour');

        $hourlyOrders = collect(range(10, 22))->mapWithKeys(fn (int $h) => [
            str_pad((string) $h, 2, '0', STR_PAD_LEFT) => (int) ($hourlyRaw[$h] ?? 0),
        ]);

        $topMenus = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereDate('orders.created_at', today())
            ->whereIn('orders.status', $paidStatuses)
            ->select('order_items.menu_name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('order_items.menu_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('manajer.dashboard', compact('omzetToday', 'ordersToday', 'avgOrder', 'hourlyOrders', 'topMenus'));
    }
}
