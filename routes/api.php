<?php

use App\Http\Controllers\Api\CreditController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ServerController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/register', [UserController::class, 'register']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/user', fn () => auth()->user());
        Route::post('/logout', [UserController::class, 'logout']);
        Route::patch('/user', [UserController::class, 'updateProfile']);
        Route::patch('/user/password', [UserController::class, 'updatePassword']);

        // Products
        Route::get('/products', [ProductController::class, 'index']);
        Route::get('/products/{product:slug}', [ProductController::class, 'show']);

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);

        // Services
        Route::get('/services', [ServiceController::class, 'index']);
        Route::get('/services/{service}', [ServiceController::class, 'show']);
        Route::post('/services/{service}/cancel', [ServiceController::class, 'cancel']);
        Route::post('/services/{service}/upgrade', [ServiceController::class, 'upgrade']);

        // Servers
        Route::get('/servers', [ServerController::class, 'index']);
        Route::get('/servers/{server}', [ServerController::class, 'show']);

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index']);
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::post('/invoices/{invoice}/pay', [InvoiceController::class, 'pay']);

        // Tickets
        Route::get('/tickets', [TicketController::class, 'index']);
        Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::post('/tickets/{ticket}/reply', [TicketController::class, 'reply']);
        Route::post('/tickets/{ticket}/close', [TicketController::class, 'close']);

        // Credits
        Route::get('/credits', fn () => auth()->user()->credits);
        Route::post('/credits/deposit', [CreditController::class, 'deposit']);

        // Payment Methods
        Route::get('/payment-methods', fn () => auth()->user()->paymentMethods);
        Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy']);
    });
});
