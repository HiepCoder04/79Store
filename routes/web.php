<?php

use App\Http\Controllers\Client\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Client\CheckoutController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Auth::routes();

Route::get('/', [ProductController::class, 'thongke'])->name('thongke');
Route::get('/home', fn() => view('client.home'))->name('home');
Route::get('/about', fn() => view('client.users.about-detail'))->name('about');
Route::get('/shop', fn() => view('client.shop'))->name('shop');
Route::get('/shop-detail', fn() => view('client.shopDetail'))->name('shop-detail');


Route::get('/', [App\Http\Controllers\Client\HomeController::class, 'indexBlog'])->name('home');
// Blog routes
Route::prefix('blogs')->name('client.blogs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Client\BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug?}', [App\Http\Controllers\Client\BlogController::class, 'category'])
        ->name('category')
        ->where('slug', '.*');
    Route::get('/{slug}', [App\Http\Controllers\Client\BlogController::class, 'show'])->name('show');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('banners', BannerController::class);
});
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Trang thống kê
    Route::get('/', function () {
        return view('admin.thongke.thongke');
    })->name('thongke');

    // CRUD sản phẩm
    Route::resource('products', ProductController::class);

    // CRUD danh mục (không có show)
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Danh sách người dùng
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'listUser'])->name('listUser');
    });
});
Route::post('admin/blogs/upload-image', [BlogController::class,'uploadImage'])
     ->name('admin.blogs.uploadImage');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function () {
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
    
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    //Thanh Toán
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});





// Route::get('/', [ProductController::class, 'thongke'])->name('thongke');
// Route::get('/home', function () {
//     return view('client.home');
// });

// HOME ROUTE
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);

Route::get('/about', function () {
    return view('client.users.about-detail');
})->name('about');

Route::get('/shop', [App\Http\Controllers\Client\ProductVariant::class,'product'])->name('shop');
Route::get('/shopDetail/{id}',[App\Http\Controllers\Client\ProductVariant::class,'productDetail'])->name('shop-detail');
