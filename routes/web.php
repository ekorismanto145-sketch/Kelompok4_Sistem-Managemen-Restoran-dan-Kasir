<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\MenuController;
use App\Http\Controllers\Customer\MockPaymentController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Kasir\PaymentController as KasirPaymentController;
use App\Http\Controllers\Kasir\PosController;
use App\Http\Controllers\Kasir\QueueController;
use App\Http\Controllers\Manajer\CategoryController;
use App\Http\Controllers\Manajer\DashboardController;
use App\Http\Controllers\Manajer\KasirController;
use App\Http\Controllers\Manajer\MenuController as ManajerMenuController;
use App\Http\Controllers\Manajer\OrderController as ManajerOrderController;
use App\Http\Controllers\Manajer\ReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MenuController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('password.email');
    Route::get('/verify-otp', [PasswordResetController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

Route::prefix('menu')->name('customer.')->group(function () {
    Route::get('/', [MenuController::class, 'index'])->name('menu');
});

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('customer.checkout');
    Route::get('/payment/{order}', [MockPaymentController::class, 'show'])->name('customer.payment.show');
    Route::post('/payment/{order}/simulate', [MockPaymentController::class, 'simulate'])->name('customer.payment.simulate');
    Route::get('/orders', [OrderController::class, 'index'])->name('customer.orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('customer.orders.show');
    Route::get('/orders/{order}/status', [OrderController::class, 'status'])->name('customer.orders.status');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('customer.orders.destroy');
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/', [QueueController::class, 'index'])->name('queue');
    Route::get('/pos', [PosController::class, 'index'])->name('pos');
    Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
    Route::get('/history', [PosController::class, 'history'])->name('history');
    Route::patch('/orders/{order}/status', [QueueController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/cash', [KasirPaymentController::class, 'cash'])->name('orders.cash');
});

Route::middleware(['auth', 'role:manajer'])->prefix('manajer')->name('manajer.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [ManajerOrderController::class, 'index'])->name('orders.index');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/menus', [ManajerMenuController::class, 'index'])->name('menus.index');
    Route::post('/menus', [ManajerMenuController::class, 'store'])->name('menus.store');
    Route::put('/menus/{menu}', [ManajerMenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{menu}', [ManajerMenuController::class, 'destroy'])->name('menus.destroy');

    Route::get('/kasir', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir', [KasirController::class, 'store'])->name('kasir.store');
    Route::delete('/kasir/{kasir}', [KasirController::class, 'destroy'])->name('kasir.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
});
