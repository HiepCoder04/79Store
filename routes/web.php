<?php
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\CheckoutController;
use App\Http\Controllers\Client\ProductVariant;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Client\ContactController as ClientContactController;
use App\Http\Controllers\Admin\VoucherController as AdminVoucherController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ForgotPasswordOtpController;
use App\Http\Controllers\Client\AccountController;

// -------------------- BLOG (CLIENT) --------------------
Route::prefix('blogs')->middleware('ban')->name('client.blogs.')->group(function () {
    Route::get('/', [App\Http\Controllers\Client\BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug?}', [App\Http\Controllers\Client\BlogController::class, 'category'])
        ->name('category')
        ->where('slug', '.*');
    Route::get('/{slug}', [App\Http\Controllers\Client\BlogController::class, 'show'])->name('show');
});

// -------------------- ADMIN ROUTES (CÓ AUTH & ROLE) --------------------
Route::middleware(['auth', 'role:admin','ban'])->prefix('admin')->name('admin.')->group(function () {


    // Trang thống kê

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý sản phẩm (cập nhật với soft delete)
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore')->withTrashed();
    Route::delete('products/{product}/force-delete', [ProductController::class, 'forceDelete'])->name('products.forceDelete')->withTrashed();

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

    // Quản lý đơn hàng
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminOrderController::class, 'show'])->name('show');
        Route::put('/{id}', [AdminOrderController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminOrderController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/restore', [AdminOrderController::class, 'restore'])->name('restore');
        Route::put('/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('updateStatus');
    });

    // Route::get('/thongke', [AdminStatisticsController::class, 'index'])->name('admin.thongke');

       // Quản lý liên hệ
Route::resource('contacts', AdminContactController::class)->except(['create', 'edit', 'store']);

    Route::get('contacts/trashed', [AdminContactController::class, 'trashed'])->name('contacts.trashed');
    Route::post('contacts/{id}/restore', [AdminContactController::class, 'restore'])->name('contacts.restore');
    Route::post('contacts/{id}/reply', [AdminContactController::class, 'sendReply'])->name('contacts.reply');
    Route::put('contacts/{id}/note', [AdminContactController::class, 'updateNote'])->name('contacts.updateNote');
});

    // Quản lý người dùng
    Route::middleware(['admin','ban'])->prefix('admin/users')->group(function () {
        Route::get('/', [UserController::class, 'listUser'])->name('admin.users.list');
    });



// -------------------- AUTH ROUTES --------------------
Route::prefix('auth')->name('auth.')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginPost')->name('loginPost');
    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'registerPost')->name('registerPost');
    Route::post('/logout', 'logout')->name('logout');

});
//gg login
    Route::get('/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
// -------------------- CLIENT (AUTHENTICATED) --------------------
Route::middleware(['auth','ban'])->group(function () {
    // Giỏ hàng
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    // Thanh toán
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/thank-you', [CheckoutController::class, 'thankYou'])->name('checkout.thankyou');

     //  Quản lý đơn hàng người dùng (Client)
   Route::prefix('orders')->name('client.orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
Route::put('/{order}/return', [OrderController::class, 'returnOrder'])->name('return');

    });
    });
    // THÔNG TIN TÀI KHOẢN (CLIENT)
Route::prefix('tai-khoan')->name('client.account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('/chinh-sua', [AccountController::class, 'edit'])->name('edit');
    Route::post('/update', [AccountController::class, 'update'])->name('update');
});

Route::get('/lien-he', [ClientContactController::class, 'showForm'])->name('client.contact.form');
Route::post('/lien-he', [ClientContactController::class, 'submitForm'])->name('client.contact.submit');

// -------------------- CỔNG THANH TOÁN --------------------
Route::post('/vnpay_payment', [PaymentController::class, 'vnpay_payment']);
Route::get('/vnpay-callback', [PaymentController::class, 'vnpayCallback'])->name('vnpay.callback');

// -------------------- TRANG CHÍNH & GIỚI THIỆU --------------------
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('ban');
Route::get('/home', [HomeController::class, 'index']);
Route::get('/about', fn() => view('client.users.about-detail'))->middleware('ban')->name('about');

// -------------------- SHOP --------------------
Route::get('/shop', [ProductVariant::class, 'product'])->middleware('ban')->name('shop');

Route::get('/shopDetail/{id}', [ProductVariant::class, 'productDetail'])->middleware('ban')->name('shop-detail');

//route su dung voucher cua user

 Route::post('/comment/store', [App\Http\Controllers\CommentController::class, 'store'])->middleware('ban')->name('comment.store');
// -------------------- SỬ DỤNG VOUCHER (USER) --------------------
Route::post('/apply-voucher', [VoucherController::class, 'apply'])->middleware('ban')->name('apply.voucher');
Route::middleware('auth')->post('/save-voucher/{id}', [VoucherController::class, 'save'])->name('voucher.save');


//phân quyền
Route::put('/ban-user', [UserController::class, 'banUser'])->name('ban-user')->middleware(['admin','ban']);
Route::put('/unban-user', [UserController::class, 'unBanUser'])->name('unban-user')->middleware(['admin','ban']);
Route::put('/update-role', [UserController::class, 'UpdateRole'])->name('update-role')->middleware(['admin','ban']);

Route::get('/forgot-password-otp', [ForgotPasswordOtpController::class, 'showEmailForm'])->name('otp.request.form')->middleware('ban');
Route::post('/forgot-password-otp', [ForgotPasswordOtpController::class, 'sendOtp'])->name('otp.request')->middleware('ban');

Route::get('/verify-otp', [ForgotPasswordOtpController::class, 'showVerifyForm'])->name('otp.verify.form')->middleware('ban');
Route::post('/verify-otp', [ForgotPasswordOtpController::class, 'verifyOtp'])->name('otp.verify')->middleware('ban');
// -------------------- CHATBOT AI --------------------
Route::post('/chatbot/chat', [App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.chat');
Route::get('/chatbot/suggestions', [App\Http\Controllers\ChatbotController::class, 'getSuggestions'])->name('chatbot.suggestions');
