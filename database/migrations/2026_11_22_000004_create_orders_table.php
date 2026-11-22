<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('queue_number')->nullable()->index();
            $table->string('type'); // dine_in | take_away
            $table->string('table_number')->nullable();
            $table->string('status')->default(OrderStatus::MenungguBayar->value)->index();
            $table->string('payment_method'); // midtrans | cash
            $table->unsignedInteger('total');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
