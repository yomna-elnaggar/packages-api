<?php

use App\Http\Controllers\PackageController;
use App\Http\Controllers\Company\PackageController as CompanyPackageController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::Resource('packages', PackageController::class);
Route::Resource('subscriptions', SubscriptionController::class);

Route::prefix('company')->middleware(['auth:user', 'type', 'company'])->group(function () {
    // Company Package Routes
   Route::get('packages/my-packages', [CompanyPackageController::class, 'index']);
   
});

