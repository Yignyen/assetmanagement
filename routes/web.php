<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Assets\AssetController;
use App\Http\Controllers\ActionLogController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Locations\LocationController;

/*
|--------------------------------------------------------------------------
| Root
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('assets.index');
});

/*
|--------------------------------------------------------------------------
| Assets
|--------------------------------------------------------------------------
*/
Route::get('/assets', [AssetController::class, 'index'])
    ->name('assets.index');

Route::get('/assets/{asset}', [AssetController::class, 'show'])
    ->name('assets.show');

Route::post('/assets/{asset}/checkout', [AssetController::class, 'checkout'])
    ->name('assets.checkout');

Route::post('/assets/{asset}/checkin', [AssetController::class, 'checkin'])
    ->name('assets.checkin');

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/
Route::resource('users', UserController::class);

/*
|--------------------------------------------------------------------------
| Locations
|--------------------------------------------------------------------------
*/
Route::resource('locations', LocationController::class);

/*
|--------------------------------------------------------------------------
| Action Logs
|--------------------------------------------------------------------------
*/
Route::get('/action_logs', [ActionLogController::class, 'index'])
    ->name('action-logs.index');
