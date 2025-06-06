<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
// Admin
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.'
], function () {
    
    // ADMIN
    Route::group([
        'prefix' => 'products',
        'as' => 'products.'
    ], function () {
        
        Route::get('/', [ProductController::class, 'listProducts'])->name('listProducts');
        
    });
});
