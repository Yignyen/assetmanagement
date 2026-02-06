<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Assets\AssetController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Locations\LocationController;
use App\Http\Controllers\ActionLogController;
use App\Http\Controllers\DashboardController;

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
    Route::get('/', [AssetController::class, 'index'])->name('index');
    Route::get('/create', [AssetController::class, 'create'])->name('create');
    Route::post('/', [AssetController::class, 'store'])->name('store');

    Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
    Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
    Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
    Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');

    Route::post('/{asset}/checkout', [AssetController::class, 'checkout'])->name('checkout');
    Route::post('/{asset}/checkin', [AssetController::class, 'checkin'])->name('checkin');
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
