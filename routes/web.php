<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Assets\AssetController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Locations\LocationController;
use App\Http\Controllers\ActionLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Assets\AssetCheckoutController;
use App\Http\Controllers\Assets\AssetCheckinController;
use App\Http\Controllers\Assets\BulkAssetsController;



/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Assets (CRUD + Domain Actions)
|--------------------------------------------------------------------------
*/
Route::prefix('assets')->name('assets.')->group(function () {

    // BULK ROUTES FIRST (IMPORTANT)
    Route::prefix('bulk')->name('bulk.')->group(function () {

        Route::post('/', [BulkAssetsController::class, 'handle'])
            ->name('handle');

        Route::get('/edit', [BulkAssetsController::class, 'editForm'])
            ->name('edit.form');

        Route::post('/edit', [BulkAssetsController::class, 'update'])
            ->name('edit.update');
        Route::get('/checkout', [BulkAssetsController::class, 'checkoutForm']) 
            ->name('checkout.form'); 
        Route::post('/checkout', [BulkAssetsController::class, 'checkoutProcess']) 
            ->name('checkout.process');
    });

    // CRUD
    Route::get('/', [AssetController::class, 'index'])->name('index');
    Route::get('/create', [AssetController::class, 'create'])->name('create');
    Route::post('/', [AssetController::class, 'store'])->name('store');

    Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
    Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
    Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
    Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');

    Route::post('/{asset}/checkout', [AssetCheckoutController::class, 'store'])
        ->name('checkout');

    Route::post('/{asset}/checkin', [AssetCheckinController::class, 'store'])
        ->name('checkin');
});


/*
|--------------------------------------------------------------------------
| Users (CRUD)
|--------------------------------------------------------------------------
*/
Route::resource('users', UserController::class);

Route::get('/users/{user}', [UserController::class, 'show'])
    ->name('users.show');


/*
|--------------------------------------------------------------------------
| Locations (CRUD)
|--------------------------------------------------------------------------
*/
Route::resource('locations', LocationController::class);

/*
|--------------------------------------------------------------------------
| Action Logs (Read-only)
|--------------------------------------------------------------------------
*/
Route::get('/action-logs', [ActionLogController::class, 'index'])
    ->name('action-logs.index');


Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
