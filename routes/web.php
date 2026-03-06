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
use Illuminate\Http\Request;



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


    // Logs
    Route::get('/action-logs', [ActionLogController::class, 'index'])
            ->name('logs');

      // Trash
    Route::get('/deleted', [AssetController::class, 'deleted'])
        ->name('deleted');

       
   



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
        
        

        //bulk delete route

        Route::get('/delete', [BulkAssetsController::class, 'bulkDeleteConfirm'])
            ->name('delete.confirm');

        Route::post('/delete', [BulkAssetsController::class, 'bulkDeleteProcess'])
            ->name('delete.process');


        // Show bulk restore page
        Route::get('/restore', [BulkAssetsController::class, 'restoreForm'])
            ->name('restore.form');

        // Process bulk restore
        Route::post('/restore', [BulkAssetsController::class, 'restoreProcess'])
            ->name('restore.process');
});

 //restore
    Route::post('/{asset}/restore', [AssetController::class, 'restore'])
    ->withTrashed()
    ->name('restore');



        


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



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

//for bulk checkout sleect


Route::get('/ajax/assets', [BulkAssetsController::class, 'ajaxAssets'])
    ->name('ajax.assets');


//for deleted assets list for select2 
Route::get('/ajax/deleted-assets', [BulkAssetsController::class, 'ajaxDeletedAssets'])
    ->name('ajax.deleted.assets');


//for model by category in creating new assets

    Route::get('/ajax/models-by-category', [AssetController::class, 'modelsByCategory'])
    ->name('ajax.models.by.category');


//for dropdown, create new categories for add asseets
 /*    Route::post('/categories/ajax-store',[CategoryController::class,'ajaxStore'])
->name('categories.ajaxStore'); */


use App\Http\Controllers\CategoryController;

Route::get('/categories/create', [CategoryController::class,'create'])
        ->name('categories.create');

Route::post('/categories/store', [CategoryController::class,'store'])
        ->name('categories.store');




//for moddel amd modal
      use App\Http\Controllers\ModelController;
use App\Models\Manufacturer;

Route::post('/ajax/models/create', [ModelController::class,'storeAjax'])
      ->name('models.ajax.store');




      // route for asset model create and store 
      Route::get('/models/create', [ModelController::class,'create'])->name('models.create');
Route::post('/models', [ModelController::class,'store'])->name('models.store');



//route for manufacturer
use App\Http\Controllers\ManufacturerController;

Route::get('/manufacturers/create', [ManufacturerController::class, 'create'])
    ->name('manufacturers.create');

Route::post('/manufacturers', [ManufacturerController::class, 'store'])
    ->name('manufacturers.store');