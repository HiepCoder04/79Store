<?php

use App\Http\Controllers\Client\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Admin\BannerController;

use App\Http\Controllers\VoucherController;


use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\OrderController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('banners', BannerController::class);
    //voucher admin
   Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class);
   //so ng da su dung voucher
    Route::get('vouchers/{voucher}/users', [\App\Http\Controllers\Admin\VoucherController::class, 'users'])->name('vouchers.users');
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

    //Thanh Toán và Đặt Hàng
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/thank-you', [CheckoutController::class, 'thankYou'])->name('checkout.thankyou');

    // Quản lý đơn hàng
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

//Cổng thanh toán vnpay
Route::post('/vnpay_payment', [PaymentController::class, 'vnpay_payment']);
Route::get('/vnpay-callback', [PaymentController::class, 'vnpayCallback'])->name('vnpay.callback');






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
//route su dung voucher cua user
 Route::post('/apply-voucher', [VoucherController::class, 'apply'])->name('apply.voucher');