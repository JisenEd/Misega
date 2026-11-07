<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MangaController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home & Catalog routes (Public)
Route::get('/', [MangaController::class, 'home'])->name('home');
Route::get('/catalogue', [MangaController::class, 'catalog'])->name('catalogue');
Route::get('/catalog', [MangaController::class, 'catalog'])->name('catalog');
Route::get('/manga/{id}', [MangaController::class, 'show'])->name('manga.show');
Route::get('/faq', fn () => Inertia::render('Faq'))->name('faq');

// Protected Routes (auth + verified)
Route::middleware(['auth', 'verified'])->group(function () {
    // Cart & Checkout routes
    Route::get('/cart', fn () => Inertia::render('Cart'))->name('cart');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{id}', [CheckoutController::class, 'success'])->name('orders.success');

    // Dashboard & Orders
    Route::get('/dashboard', fn () => redirect()->route('orders.index'))->name('dashboard');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
