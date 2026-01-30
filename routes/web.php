<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ActionLogController;

Route::get('/', function () {
    return view('component.layout');
});





Route::get('/assets/{asset}', [AssetController::class, 'show'])
    ->name('assets.show');

Route::post('/assets/{asset}/assign', [AssetController::class, 'checkout'])
    ->name('assets.assign');

Route::post('/assets/{asset}/unassign', [AssetController::class, 'checkin'])
    ->name('assets.unassign');



Route::get('/assets', [AssetController::class, 'index'])
    ->name('assets.index');






Route::get('/action_logs', [ActionLogController::class, 'index'])
    ->name('action-logs.index');
