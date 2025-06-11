<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\CartController;


Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
});




Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('loginPost');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerPost'])->name('registerPost');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
});





Route::get('/', [ProductController::class, 'thongke'])->name('thongke');
Route::get('/home', function () {
    return view('client.home');
});






//ADMIN
Route::group([
    'prefix' => 'admin',
    'as' => 'admin.'
], function () {

    Route::group([
        'prefix' => 'users',
        'as' => 'users.'
    ], function () {
        Route::get('/', [UserController::class, 'listUser'])->name('listUser');
    });

    // CATEGORY CRUD ROUTES

    Route::resource('categories', CategoryController::class)->except(['show']);
});



//CLIENT
Route::group([
    'prefix' => 'client',
    'as' => 'client.'
], function () {

    // HOME ROUTE
    Route::get('/home', function () {
        return view('client.home');
    })->name('home');

    Route::get('/about', function () {
        return view('client.users.about-detail');
    })->name('about');
});
