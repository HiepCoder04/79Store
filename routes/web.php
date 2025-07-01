<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\ProductVariant;

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AdminStatisticsController;

// -------------------- BLOG (CLIENT) --------------------
Route::prefix('blogs')->name('client.blogs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Client\BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug?}', [App\Http\Controllers\Client\BlogController::class, 'category'])
        ->name('category')
        ->where('slug', '.*');
    Route::get('/{slug}', [App\Http\Controllers\Client\BlogController::class, 'show'])->name('show');
});

// -------------------- ADMIN ROUTES (CÓ AUTH & ROLE) --------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Trang thống kê

    Route::get('/dashboard', [AdminStatisticsController::class, 'index'])->name('thongke');

    // Quản lý sản phẩm
    Route::resource('products', ProductController::class);

    // Quản lý danh mục
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Quản lý blog & danh mục blog
    Route::resource('blogs', BlogController::class)->except(['show']);
    Route::resource('category_blogs', BlogCategoryController::class)->except(['show']);
    Route::post('blogs/upload-image', [BlogController::class, 'uploadImage'])->name('blogs.uploadImage');

    // Quản lý banner
    Route::resource('banners', BannerController::class);

    // Quản lý voucher
    Route::resource('vouchers', AdminVoucherController::class);
    Route::get('vouchers/{voucher}/users', [AdminVoucherController::class, 'users'])->name('vouchers.users');

    // Quản lý người dùng
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'listUser'])->name('list');
    });

    // Quản lý đơn hàng
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
        Route::delete('/{id}', [AdminOrderController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [AdminOrderController::class, 'restore'])->name('restore');
    });
});

// -------------------- AUTH ROUTES --------------------
Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginPost')->name('loginPost');
    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'registerPost')->name('registerPost');
    Route::post('/logout', 'logout')->name('logout');
});

// -------------------- CLIENT (AUTHENTICATED) --------------------
Route::middleware('auth')->group(function () {
    // Giỏ hàng
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Thanh toán
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/thank-you', [CheckoutController::class, 'thankYou'])->name('checkout.thankyou');

    // Quản lý đơn hàng cá nhân
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});

// -------------------- CỔNG THANH TOÁN --------------------
Route::post('/vnpay_payment', [PaymentController::class, 'vnpay_payment']);
Route::get('/vnpay-callback', [PaymentController::class, 'vnpayCallback'])->name('vnpay.callback');

// -------------------- TRANG CHÍNH & GIỚI THIỆU --------------------
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/about', fn() => view('client.users.about-detail'))->name('about');

// -------------------- SHOP --------------------
Route::get('/shop', [ProductVariant::class, 'product'])->name('shop');

Route::get('/shopDetail/{id}', [ProductVariant::class, 'productDetail'])->name('shop-detail');

//route su dung voucher cua user

 Route::post('/comment/store', [App\Http\Controllers\CommentController::class, 'store'])->name('comment.store');
// -------------------- SỬ DỤNG VOUCHER (USER) --------------------
Route::post('/apply-voucher', [VoucherController::class, 'apply'])->name('apply.voucher');
