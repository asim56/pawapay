<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PawaPayController;
use App\Http\Controllers\PawaPayWebhookController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/product/payment/create/url', [ProductController::class, 'showPaymentLinkCreator']);
Route::post('/product/payment/create/url/submit', [ProductController::class, 'submitPaymentLinkCreator'])->name('product_payment_create_url');
Route::get('/product/payment/{token}', [ProductController::class, 'productPayment'])->name('product_payment');
Route::get('/product/payment/submit', [ProductController::class, 'submitProductPayment'])->name('product_payment');

Route::get('/', [PawaPayController::class, 'index']);
Route::post('/pay', [PawaPayController::class, 'initiatePayment']);
Route::get('/pawapay/webhook', [PawaPayWebhookController::class, 'index']);
Route::get('/payment/status', [PawaPayController::class, 'paymentStatus']);


require __DIR__.'/auth.php';
