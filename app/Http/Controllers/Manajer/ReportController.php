<?php

namespace App\Http\Controllers\Manajer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    private function getReportData(Request $request): array
    {
        $request->validate([
            'start_date' => ['nullable', 'date', 'before_or_equal:today'],
            'end_date' => ['nullable', 'date', 'before_or_equal:today'],
        ], [
            'start_date.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari hari ini.',
            'end_date.before_or_equal' => 'Tanggal akhir tidak boleh lebih dari hari ini.',
        ]);

        $startDate = $request->date('start_date') ?? now()->startOfMonth();
        $endDate = $request->date('end_date') ?? now()->endOfDay();

        $paidStatuses = [
            OrderStatus::Diproses->value,
            OrderStatus::Siap->value,
            OrderStatus::Selesai->value,
        ];

        $orders = Order::with('user')
            ->whereIn('status', $paidStatuses)
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->latest()
            ->get();

        $totalOmzet = $orders->sum('total');
        $byMethod = $orders->groupBy('payment_method')->map->sum('total');

        $perSlide = 50;
        $totalOrders = $orders->count();
        $totalSlides = max(1, (int) ceil($totalOrders / $perSlide));
        $currentSlide = min(max(1, (int) $request->input('slide', 1)), $totalSlides);
        $pagedOrders = $orders->forPage($currentSlide, $perSlide)->values();

        $topMenus = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', $paidStatuses)
            ->whereBetween('orders.created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
            ->select('order_items.menu_name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->groupBy('order_items.menu_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return compact(
            'orders',
            'pagedOrders',
            'totalOrders',
            'currentSlide',
            'totalSlides',
            'perSlide',
            'totalOmzet',
            'byMethod',
            'topMenus',
            'startDate',
            'endDate'
        );
    }

    public function index(Request $request): View
    {
        return view('manajer.reports', $this->getReportData($request));
    }

    public function exportExcel(Request $request): Response
    {
        $data = $this->getReportData($request);
        $filename = 'laporan-penjualan-' . $data['startDate']->format('Y-m-d') . '-sd-' . $data['endDate']->format('Y-m-d') . '.xls';

        return response()
            ->view('manajer.reports-excel', $data)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
