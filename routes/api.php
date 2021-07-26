<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::post('checkout/add', [CheckoutController::class, 'store']);
Route::post('checkout/remove', [CheckoutController::class, 'destroy']);
