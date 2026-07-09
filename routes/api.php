<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('v1')->group(function () {
    Route::get('/user', fn (Request $r) => $r->user());

    // Products
    Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('/products/{product}', [\App\Http\Controllers\Api\ProductController::class, 'show']);

    // Orders
    Route::get('/orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::get('/orders/{order}', [\App\Http\Controllers\Api\OrderController::class, 'show']);

    // Servers
    Route::get('/servers', [\App\Http\Controllers\Api\ServerController::class, 'index']);
    Route::get('/servers/{server}', [\App\Http\Controllers\Api\ServerController::class, 'show']);

    // Invoices
    Route::get('/invoices', [\App\Http\Controllers\Api\InvoiceController::class, 'index']);
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\Api\InvoiceController::class, 'show']);
});
