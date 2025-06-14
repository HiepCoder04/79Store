<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\CartController;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::get('/', [ProductController::class, 'thongke'])->name('thongke');
Route::get('/home', fn() => view('client.home'))->name('home');
Route::get('/about', fn() => view('client.users.about-detail'))->name('about');
Route::get('/shop', fn() => view('client.shop'))->name('shop');
Route::get('/shop-detail', fn() => view('client.shopDetail'))->name('shop-detail');


Route::get('/', [App\Http\Controllers\Client\HomeController::class, 'index'])->name('home');
// Blog routes
Route::prefix('blogs')->name('client.blogs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Client\BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug?}', [App\Http\Controllers\Client\BlogController::class, 'category'])
        ->name('category')
        ->where('slug', '.*');
    Route::get('/{slug}', [App\Http\Controllers\Client\BlogController::class, 'show'])->name('show');
});
Route::post('admin/blogs/upload-image', [BlogController::class,'uploadImage'])
     ->name('admin.blogs.uploadImage');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginPost')->name('loginPost');
    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'registerPost')->name('registerPost');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Dashboard
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => view('admin.thongke.thongke'))->name('home');

    // Products
    Route::resource('products', ProductController::class);

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Blogs
    Route::resource('blogs', BlogController::class)->except(['show']);
    Route::resource('category_blogs', BlogCategoryController::class)->except(['show']);

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'listUser'])->name('list');
    });
});

/*
|--------------------------------------------------------------------------
| Cart (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    // thêm các action update/remove nếu cần
});
