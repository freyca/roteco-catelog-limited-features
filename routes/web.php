<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware(Authenticate::class)->group(function () {
    Route::redirect('/', '/categorias')->name('home');

    /** Checkout */
    Route::group(['as' => 'checkout.'], function () {
        Route::get('carrito', [CartController::class, 'index'])->name('cart');

        // POST requests sent to checkout are managed by livewire in App\Livewire\CheckoutForm
        Route::get('redirectToPayment/{order}', [PaymentController::class, 'redirectToPayment'])
            ->name('redirectToPayment');
    });

    /** Payment */
    Route::group(['as' => 'payment.'], function () {
        Route::get('pago-completo/{order}', [PaymentController::class, 'orderFinishedOk'])
            ->name('purchase-complete');
    });

    /** Products */
    Route::get('/productos', [ProductController::class, 'all'])->name('product-list');
    Route::get('producto/{product}', [ProductController::class, 'product'])->name('product');

    /** Categories */
    Route::get('categorias', [CategoryController::class, 'index'])->name('category-list');
    Route::get('{category}', [CategoryController::class, 'category'])->name('category');
});
