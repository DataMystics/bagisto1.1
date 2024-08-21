<?php

use Illuminate\Support\Facades\Route;
use Webkul\Paystack\Http\Controllers\SmartButtonController;
use Webkul\Paystack\Http\Controllers\StandardController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('paystack/standard')->group(function () {
        Route::get('/redirect', [StandardController::class, 'redirect'])->name('paystack.standard.redirect');

        Route::get('/success', [StandardController::class, 'success'])->name('paystack.standard.success');

        Route::get('/cancel', [StandardController::class, 'cancel'])->name('paystack.standard.cancel');
    });

    // Route::prefix('paystack/smart-button')->group(function () {
    //     Route::get('/create-order', [SmartButtonController::class, 'createOrder'])->name('paystack.smart-button.create-order');

    //     Route::post('/capture-order', [SmartButtonController::class, 'captureOrder'])->name('paystack.smart-button.capture-order');
    // });
});

Route::post('paystack/standard/ipn', [StandardController::class, 'ipn'])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
    ->name('paystack.standard.ipn');
