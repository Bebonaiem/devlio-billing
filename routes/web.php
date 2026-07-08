<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InvoiceController as AdminInvoiceController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\Webhook\PayPalController;
use App\Http\Controllers\Webhook\StripeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'index'])->name('storefront');
Route::get('/products/{slug}', [StorefrontController::class, 'category'])->name('storefront.category');
Route::get('/product/{slug}', [StorefrontController::class, 'product'])->name('storefront.product');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::patch('/update/{item}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{item}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
        Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon.apply');
        Route::delete('/coupon', [CartController::class, 'removeCoupon'])->name('coupon.remove');
    });

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/pay/{invoice}', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    Route::post('/currency', [CheckoutController::class, 'setCurrency'])->name('currency.set');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/services', [DashboardController::class, 'services'])->name('services');
        Route::get('/services/{service}', [DashboardController::class, 'serviceDetail'])->name('service-detail');
        Route::get('/invoices', [DashboardController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}', [DashboardController::class, 'invoiceDetail'])->name('invoice-detail');
        Route::get('/tickets', [DashboardController::class, 'tickets'])->name('tickets');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
        Route::patch('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
        Route::patch('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.password');
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

        Route::get('/products/categories', [AdminProductController::class, 'categories'])->name('products.categories');
        Route::post('/products/categories', [AdminProductController::class, 'storeCategory'])->name('products.categories.store');
        Route::patch('/products/categories/{category}', [AdminProductController::class, 'updateCategory'])->name('products.categories.update');
        Route::delete('/products/categories/{category}', [AdminProductController::class, 'destroyCategory'])->name('products.categories.destroy');

        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/products/{product}/plans', [AdminProductController::class, 'plans'])->name('products.plans');
        Route::post('/products/{product}/plans', [AdminProductController::class, 'storePlan'])->name('products.plans.store');
        Route::patch('/products/{product}/plans/{plan}', [AdminProductController::class, 'updatePlan'])->name('products.plans.update');
        Route::delete('/products/{product}/plans/{plan}', [AdminProductController::class, 'destroyPlan'])->name('products.plans.destroy');

        Route::patch('/products/{product}/config-options', [AdminProductController::class, 'updateConfigOptions'])->name('products.config-options.update');

        Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        Route::get('/services/{service}', [AdminServiceController::class, 'show'])->name('services.show');
        Route::post('/services/{service}/suspend', [AdminServiceController::class, 'suspend'])->name('services.suspend');
        Route::post('/services/{service}/unsuspend', [AdminServiceController::class, 'unsuspend'])->name('services.unsuspend');
        Route::post('/services/{service}/terminate', [AdminServiceController::class, 'terminate'])->name('services.terminate');
        Route::delete('/services/{service}', [AdminServiceController::class, 'destroy'])->name('services.destroy');

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
        Route::post('/tickets/{ticket}/assign', [AdminTicketController::class, 'assign'])->name('tickets.assign');
        Route::post('/tickets/{ticket}/close', [AdminTicketController::class, 'close'])->name('tickets.close');
        Route::post('/tickets/{ticket}/reopen', [AdminTicketController::class, 'reopen'])->name('tickets.reopen');
        Route::delete('/tickets/{ticket}', [AdminTicketController::class, 'destroy'])->name('tickets.destroy');
    });
});

Route::prefix('webhook')->name('webhook.')->group(function () {
    Route::post('/stripe', [StripeController::class, 'handleWebhook'])->name('stripe');
    Route::post('/paypal', [PayPalController::class, 'handleWebhook'])->name('paypal');
});
