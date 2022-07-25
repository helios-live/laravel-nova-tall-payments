<?php

use IdeaToCode\LaravelNovaTallPayments\Controllers\OrderController;
use IdeaToCode\LaravelNovaTallPayments\Controllers\TestController;
use Illuminate\Support\Facades\Route;

//Route::name('larapay.')->middleware('web')


Route::middleware(['web'])->group(function () {
	Route::middleware(['auth:sanctum', 'verified'])->name('larapay.')->group(function () {
		Route::get('/pay/{invoice}', [TestController::class, 'newFlow'])->name('pay');

		Route::any('/gateway/{gateway}/{invoice}', [TestController::class, 'gw'])->name('gateway');

		// Route::get('/test-123', function(){
		// 	return view('larapay::emails.invoice-created');
		// });

		Route::get('/order', [OrderController::class, 'getProductList'])->name('order.product-list');
		Route::post('/order', [OrderController::class, 'postProductOrder'])->name('order.product');
	});


	Route::get('payment', [TestController::class, 'payment'])->name('payment');
	Route::get('cancel', [TestController::class, 'cancel'])->name('payment.cancel');
	Route::get('payment/success', [TestController::class, 'success'])->name('payment.success');
	Route::get('invoice/{invoice}', [TestController::class, 'showInvoice'])->name('invoice.show');
});