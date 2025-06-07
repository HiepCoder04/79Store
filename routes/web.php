<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.'
], function () {

    // PRODUCT ROUTES
    Route::group([
        'prefix' => 'products',
        'as' => 'products.'
    ], function () {
        Route::get('/', [ProductController::class, 'listProducts'])->name('listProducts');
    });

    // CATEGORY CRUD ROUTES
    Route::resource('categories', CategoryController::class)->except(['show']);
});
