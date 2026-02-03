<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Assets\AssetController;
use App\Http\Controllers\ActionLogController;
use App\Http\Controllers\Users\UserController;



Route::get('/', function () {
    return view('component.layout');
});





Route::get('/assets', [AssetController::class, 'index'])
    ->name('assets.index');

Route::get('/assets/{asset}', [AssetController::class, 'show'])
    ->name('assets.show');

/* CHECKOUT */
Route::post('/assets/{asset}/checkout', [AssetController::class, 'checkout'])
    ->name('assets.checkout');

/* CHECKIN */
Route::post('/assets/{asset}/checkin', [AssetController::class, 'checkin'])
    ->name('assets.checkin');






Route::get('/action_logs', [ActionLogController::class, 'index'])
    ->name('action-logs.index');

/* 
for delete user */

/* Route::delete('/users/{user}', [UserController::class, 'destroy'])
     ->name('users.destroy');

     //for index

Route::get('/users', [UserController::class, 'index'])
    ->name('users.index');
 */


Route::resource('users', UserController::class);




use App\Http\Controllers\Locations\LocationController;

Route::resource('locations', LocationController::class);


