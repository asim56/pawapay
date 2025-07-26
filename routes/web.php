<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PawaPayController;
use App\Http\Controllers\PawaPayWebhookController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {

});


//Route::get('/product/payment/create/url', [ProductController::class, 'showPaymentLinkCreator']);
//Route::post('/product/payment/create/url/submit', [ProductController::class, 'submitPaymentLinkCreator'])->name('product_payment_create_url');

Route::post('/product/payment/submit', [ProductController::class, 'submitProductPayment'])->name('product_payment');

//Route::get('/', [PawaPayController::class, 'index']);
Route::get('/product/payment/{token}', [PawaPayController::class, 'productPayment'])->name('product_payment');
Route::post('/pay', [PawaPayController::class, 'initiatePayment'])->name('pay');
Route::get('/pawapay/webhook', [PawaPayWebhookController::class, 'index']);
Route::get('/payment/status', [PawaPayController::class, 'paymentStatus']);


require __DIR__.'/auth.php';
