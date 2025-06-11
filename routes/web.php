<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;



Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
    Route::get('/', function(){
        return view('admin.thongke.thongke');
    })->name('home-admin');
    //user
    Route::group([
        'prefix' => 'users',
        'as' => 'users.'
    ], function () {
        Route::get('/', [UserController::class, 'listUser'])->name('listUser');
    });

    // CATEGORY CRUD ROUTES

    Route::resource('categories', CategoryController::class)->except(['show']);
});



Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('loginPost');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerPost'])->name('registerPost');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Route::middleware(['auth', 'role:admin'])->group(function () {
//     Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
// });

// // Staff
// Route::middleware(['auth', 'role:staff'])->group(function () {
//     Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff.dashboard');
// });

// // Customer và Guest
// Route::middleware(['auth', 'role:customer,guest'])->group(function () {
//     Route::get('/home', [UserController::class, 'home'])->name('user.home');
// });
Route::get('/', [ProductController::class, 'thongke'])->name('thongke');

Route::get('/home', function () {
    return view('client.home');
});

  // HOME ROUTE
  Route::get('/home', function () {
    return view('client.home');
})->name('home');
Route::get('/', function () {
return view('client.home');});

Route::get('/about', function () {
    return view('client.users.about-detail');
})->name('about');

Route::get('/shop', function () {
    return view('client.shop');
})->name('shop');
Route::get('/shopDetail', function () {
    return view('client.shopDetail');
})->name('shop-detail');