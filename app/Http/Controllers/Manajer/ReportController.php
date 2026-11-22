<?php

namespace App\Http\Controllers\Manajer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->date('start_date') ?? now()->startOfMonth();
        $endDate = $request->date('end_date') ?? now()->endOfDay();

        $paidStatuses = [
            OrderStatus::Diproses->value,
            OrderStatus::Siap->value,
            OrderStatus::Selesai->value,
        ];

        $orders = Order::with('user')
            ->whereIn('status', $paidStatuses)
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->latest()
            ->get();

        $totalOmzet = $orders->sum('total');
        $byMethod = $orders->groupBy('payment_method')->map->sum('total');

        $topMenus = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', $paidStatuses)
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->select('order_items.menu_name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('order_items.menu_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('manajer.reports', compact('orders', 'totalOmzet', 'byMethod', 'topMenus', 'startDate', 'endDate'));
    }
}
