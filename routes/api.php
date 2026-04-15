<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentCallbackController;

Route::post('midtrans/callback', [PaymentCallbackController::class, 'callback']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
