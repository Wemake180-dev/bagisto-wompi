<?php

use Illuminate\Support\Facades\Route;
use Webkul\Wompi\Http\Controllers\PaymentController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('wompi')->group(function () {
        Route::get('/redirect', [PaymentController::class, 'redirect'])->name('wompi.redirect');

        Route::get('/success', [PaymentController::class, 'success'])->name('wompi.success');

        Route::get('/cancel', [PaymentController::class, 'cancel'])->name('wompi.cancel');

        Route::post('/transaction-status', [PaymentController::class, 'getTransactionStatus'])
            ->name('wompi.transaction.status');
    });
});

Route::post('wompi/webhook', [PaymentController::class, 'webhook'])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
    ->name('wompi.webhook');
