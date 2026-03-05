<?php

use App\Http\Controllers\PackageController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::Resource('packages', PackageController::class);
Route::Resource('subscriptions', SubscriptionController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
