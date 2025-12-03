<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LandingsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeoController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware(Authenticate::class)->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Route::get('sobre-nosotros', [LandingsController::class, 'aboutUs'])->name('about-us');
    // Route::get('politica-de-privacidad', [LandingsController::class, 'privacyPolicy'])->name('privacy-policy');
    // Route::get('contacto', [LandingsController::class, 'contact'])->name('contact');

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

    /** Complements */
    // Route::get('/complementos-producto', [ProductController::class, 'complements'])->name('complement-list');
    // Route::get('complemento/{productComplement}', [ProductController::class, 'productComplement'])->name('complement');

    /** Spare parts */
    // Route::get('/piezas-de-repuesto', [ProductController::class, 'spareParts'])->name('spare-part-list');
    // Route::get('pieza-de-repuesto/{productSparePart}', [ProductController::class, 'productSparePart'])->name('spare-part');

    /** Seo URL's */
    // Route::name('seo.')->group(function () {
    //    Route::get('/desbrozadoras-por-menos-de-1000-euros', [SeoController::class, 'desbrozadorasBaratas'])->name('desbrozadoras-baratas');
    // });

    /** Categories */
    Route::get('categorias', [CategoryController::class, 'index'])->name('category-list');
    Route::get('{category}', [CategoryController::class, 'category'])->name('category');
});
