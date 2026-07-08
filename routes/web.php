<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ServerController as AdminServerController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
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
        Route::post('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminDashboardController::class, 'destroyUser'])->name('users.destroy');

        Route::resource('products', AdminProductController::class);
        Route::resource('plans', AdminPlanController::class);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/suspend', [AdminOrderController::class, 'suspend'])->name('orders.suspend');
        Route::post('/orders/{order}/unsuspend', [AdminOrderController::class, 'unsuspend'])->name('orders.unsuspend');
        Route::post('/orders/{order}/terminate', [AdminOrderController::class, 'terminate'])->name('orders.terminate');
        Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

        Route::get('/servers', [AdminServerController::class, 'index'])->name('servers.index');
        Route::get('/servers/{server}', [AdminServerController::class, 'show'])->name('servers.show');
        Route::delete('/servers/{server}', [AdminServerController::class, 'destroy'])->name('servers.destroy');

        Route::get('/invoices', [AdminInvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{invoice}/paid', [AdminInvoiceController::class, 'markPaid'])->name('invoices.paid');
        Route::post('/invoices/{invoice}/overdue', [AdminInvoiceController::class, 'markOverdue'])->name('invoices.overdue');
        Route::post('/invoices/{invoice}/cancel', [AdminInvoiceController::class, 'markCancelled'])->name('invoices.cancel');
        Route::delete('/invoices/{invoice}', [AdminInvoiceController::class, 'destroy'])->name('invoices.destroy');

        Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [AdminTransactionController::class, 'show'])->name('transactions.show');
        Route::post('/transactions/{transaction}/refund', [AdminTransactionController::class, 'refund'])->name('transactions.refund');

        Route::get('/commissions', [AdminTransactionController::class, 'commissions'])->name('commissions.index');
        Route::post('/commissions/{commission}/approve', [AdminTransactionController::class, 'approveCommission'])->name('commissions.approve');
        Route::post('/commissions/{commission}/pay', [AdminTransactionController::class, 'payCommission'])->name('commissions.pay');

        Route::get('/tickets', [AdminTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [AdminTicketController::class, 'show'])->name('tickets.show');
        Route::post('/tickets/{ticket}/reply', [AdminTicketController::class, 'reply'])->name('tickets.reply');
        Route::post('/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');
        Route::post('/tickets/{ticket}/reopen', [AdminTicketController::class, 'reopen'])->name('tickets.reopen');
        Route::delete('/tickets/{ticket}', [AdminTicketController::class, 'destroy'])->name('tickets.destroy');
    });
});

Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::post('/stripe', [StripeController::class, 'handleWebhook'])->name('stripe');
    Route::post('/paypal', [PayPalController::class, 'handleWebhook'])->name('paypal');
});
