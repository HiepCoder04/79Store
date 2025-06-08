<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
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

    Route::group([
        'prefix' => 'users',
        'as' => 'users.'
    ], function () {
        Route::get('/', [UserController::class, 'listUser'])->name('listUser');
    });

    // CATEGORY CRUD ROUTES
    Route::resource('categories', CategoryController::class)->except(['show']);
});
