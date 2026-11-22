<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Create an order with server-calculated total and price snapshots.
     *
     * @param  array<int, array{menu_id:int, quantity:int, spicy_level?:int|null, note?:string|null}>  $items
     */
    public function checkout(
        User $user,
        array $items,
        string $type,
        ?string $tableNumber,
        string $paymentMethod
    ): Order {
        return DB::transaction(function () use ($user, $items, $type, $tableNumber, $paymentMethod): Order {
            $menuIds = collect($items)->pluck('menu_id')->unique()->all();
            $menus = Menu::whereIn('id', $menuIds)->where('is_available', true)->get()->keyBy('id');

            $subtotal = 0;
            $orderItemsData = [];

            foreach ($items as $item) {
                $menu = $menus->get($item['menu_id']);
                abort_unless($menu !== null, 422, 'Menu tidak tersedia.');

                $qty = max(1, (int) $item['quantity']);
                $spicyLevel = $menu->has_spicy_level ? (int) ($item['spicy_level'] ?? 0) : null;
                $extraFee = match (true) {
                    $spicyLevel !== null && $spicyLevel > 6 => 2000,
                    $spicyLevel !== null && $spicyLevel > 4 => 1000,
                    default => 0,
                };
                $unitPrice = $menu->price + $extraFee;
                $subtotal += $unitPrice * $qty;

                $orderItemsData[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'price' => $unitPrice,
                    'quantity' => $qty,
                    'spicy_level' => $spicyLevel,
                    'note' => $item['note'] ?? null,
                ];
            }

            $tax = (int) round($subtotal * 0.10);
            $total = $subtotal + $tax;

            $order = Order::create([
                'code' => 'ORD-'.strtoupper(Str::random(6)),
                'user_id' => $user->id,
                'type' => $type,
                'table_number' => $type === 'dine_in' ? $tableNumber : null,
                'status' => OrderStatus::MenungguBayar,
                'payment_method' => $paymentMethod,
                'total' => $total,
            ]);

            $order->items()->createMany($orderItemsData);

            return $order;
        });
    }

    /**
     * Mark order as paid and assign a daily sequential queue number (A-001, A-002, ...).
     */
    public function markPaid(Order $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($locked->status !== OrderStatus::MenungguBayar) {
                return $locked;
            }

            $todayCount = Order::whereDate('paid_at', today())
                ->whereNotNull('queue_number')
                ->lockForUpdate()
                ->count();

            $queueNumber = 'A-'.str_pad((string) ($todayCount + 1), 3, '0', STR_PAD_LEFT);

            $locked->update([
                'status' => OrderStatus::Diproses,
                'queue_number' => $queueNumber,
                'paid_at' => now(),
            ]);

            return $locked;
        });
    }
}
