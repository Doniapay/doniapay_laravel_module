<?php
use Illuminate\Support\Facades\Route;
use Modules\DoniaPay\Http\Controllers\DoniaPayController;
Route::group(['prefix' => 'payment/doniapay', 'middleware' => ['web']], function () {
    Route::get('/success', [DoniaPayController::class, 'success'])->name('doniapay.success');
    Route::get('/cancel', [DoniaPayController::class, 'cancel'])->name('doniapay.cancel');
});
