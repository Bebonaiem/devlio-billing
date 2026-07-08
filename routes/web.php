<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Webhook\PayPalController;
use App\Http\Controllers\Webhook\StripeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'index'])->name('storefront');
Route::get('/product/{product}', [StorefrontController::class, 'product'])->name('storefront.product');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/checkout/{plan}', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/{plan}', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/{order}/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/servers', [DashboardController::class, 'servers'])->name('servers');
        Route::get('/servers/{server}', [DashboardController::class, 'serverDetail'])->name('server-detail');
        Route::get('/invoices', [DashboardController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}', [DashboardController::class, 'invoiceDetail'])->name('invoice-detail');
        Route::get('/tickets', [DashboardController::class, 'tickets'])->name('tickets');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::get('/affiliate', [DashboardController::class, 'affiliate'])->name('affiliate');
    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
        Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/users/{user}', [AdminDashboardController::class, 'userDetail'])->name('user-detail');

        Route::resource('products', AdminProductController::class);
        Route::resource('plans', AdminPlanController::class);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/suspend', [AdminOrderController::class, 'suspend'])->name('orders.suspend');
        Route::post('/orders/{order}/unsuspend', [AdminOrderController::class, 'unsuspend'])->name('orders.unsuspend');
        Route::post('/orders/{order}/terminate', [AdminOrderController::class, 'terminate'])->name('orders.terminate');
        Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');
    });
});

Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::post('/stripe', [StripeController::class, 'handleWebhook'])->name('stripe');
    Route::post('/paypal', [PayPalController::class, 'handleWebhook'])->name('paypal');
});
