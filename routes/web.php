<?php

use App\Http\Controllers\Admin\AdminSiteController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\UserController as UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SiteController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Models\Cart;
use Faker\Provider\ar_EG\Payment;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DepositController;
use App\Http\Controllers\Admin\WebConfigController;
use App\Http\Controllers\Admin\GatewayController;
use App\Http\Controllers\Admin\GiftCodeController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Seller\SellerProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/// Site route
Route::get('/', [SiteController::class, 'index'])->name('site.index');

/// upload image
Route::get('image-upload-preview', [ImageUploadController::class, 'index']);
Route::post('upload-image', [ImageUploadController::class, 'store']);

/// product route
Route::name('products.')->prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{id}/show', [ProductController::class, 'show'])->name('show');
    Route::get('/create', [ProductController::class, 'create']);
    Route::post('/create', [ProductController::class, 'store']);
    Route::get('/{id}/edit', [ProductController::class, 'edit']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::post('/filter', [ProductController::class, 'filter'])->name('filter');
});

Route::name('categories.')->prefix('categories')->group(function () {
    Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
});


Route::name('user.')->prefix('user')->middleware('auth', 'BannedMiddleware')->group(function () {

    /// CART
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::get('/load', [CartController::class, 'loadCart'])->name('load');
        Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
        Route::post('/confirm', [CartController::class, 'confirm'])->name('confirm');
        Route::delete('/remove', [CartController::class, 'remove'])->withoutMiddleware([VerifyCsrfToken::class])->name('remove');
        Route::post('/add-to-cart', [CartController::class, 'addToCart'])->withoutMiddleware([VerifyCsrfToken::class])->name('add');
    });


    /// DEPOSIT
    Route::prefix('deposit')->name('deposit.')->group(function () {
        Route::get('/', [PaymentController::class, 'deposit'])->name("index");
        Route::get('/{id}', [PaymentController::class, 'depositDetails'])->name("details");
        Route::post('/preview', [PaymentController::class, 'depositPreview'])->name("preview");
        Route::post('/confirm', [PaymentController::class, 'depositConfirm'])->name("confirm");
    });


    /// ORDER
    Route::prefix('orders')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name("index");
        Route::get('/details/{id}', [OrderController::class, 'details'])->name("details");
        Route::get('/report/{id}', [OrderController::class, 'report'])->name("report");
        Route::delete('/report/{id}', [OrderController::class, 'delete'])->name("delete");
        Route::post('/report/{id}', [OrderController::class, 'storeReport'])->name("reportSend");
    });


    /// SETTINGS
    Route::get('/setting', [UserController::class, 'setting'])->name('setting');
    Route::post('/setting/update', [UserController::class, 'settinglord'])->name('setting.update');


    /// UPGRADE
    Route::get('/upgrade', [UserController::class, 'upgrade'])->name('upgrade');
    Route::post('/upgrade/confirm', [UserController::class, 'confirmUpgrade'])->name('confirmUpgrade');


    /// OTHERS
    Route::get('/trans', [PaymentController::class, 'history'])->name('trans');

    Route::post('/giftcode/apply', [UserController::class, 'applyGiftCode'])->name('applyGiftCode');
});

// route admin
Route::name('admin.')->prefix('admin')->middleware('auth', 'checkLogin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'Dashboard'])->name('dashboard');

    Route::get('/categories', [CategoriesController::class, 'Categories']);
    Route::get('/categories/create', [CategoriesController::class, 'createCategories']);
    Route::post('/categories/create', [CategoriesController::class, 'storeCategories'])->name('storeCategory');
    Route::get('/categories/{id}/edit', [CategoriesController::class, 'editCategories']);
    Route::put('/categories/{id}/update', [CategoriesController::class, 'updateCategories']);
    Route::delete('/categories/{id}/delete', [CategoriesController::class, 'destroyCategories']);

    Route::get('/gateways', [GatewayController::class, 'index'])->name('gateway.index');
    Route::get('/gateways/add', [GatewayController::class, 'add'])->name('gateway.add');
    Route::get('/gateway/{id}', [GatewayController::class, 'show'])->name('gateway.show');
    Route::post('/gateway/{id}/update', [GatewayController::class, 'update'])->name('gateway.update');
    Route::post('/gateway/store', [GatewayController::class, 'store'])->name('gateway.store');

    Route::get('/deposit', [DepositController::class, 'Deposit'])->name('deposit.index');
    Route::get('/deposit/{id}/edit', [DepositController::class, 'editDeposit']);
    Route::put('/deposit/{id}/accept', [DepositController::class, 'updateAcceptDeposit']);
    Route::put('/deposit/{id}/deny', [DepositController::class, 'updateDenyDeposit']);

    Route::get('/user', [AdminUserController::class, 'User'])->name('user.index');
    Route::put('/user/ban/{id}', [AdminUserController::class, 'banUser']);
    Route::get('/user/edit/{id}', [AdminUserController::class, 'editUser']);
    Route::post('/user/update/{id}', [AdminUserController::class, 'updateUser'])->name('confirmUpdateUser');

    Route::get('/giftcodes', [GiftCodeController::class, 'index'])->name('giftcode.index');
    Route::post('/giftcodes/store', [GiftCodeController::class, 'store'])->name('giftcode.store');
    Route::delete('/giftcode/{id}/remove', [GiftCodeController::class, 'remove'])->name('giftcode.remove');

    Route::get('/ads', [])->name('ads.index');

    Route::get('/search', [AdminUserController::class, 'searchUser']);
    Route::get('/web-config', [WebConfigController::class, 'index']);
    Route::put('/web-config/update', [WebConfigController::class, 'updateWebConfig'])->name('updateWebConfig');
});

// route seller
Route::name('seller.')->prefix('seller')->middleware('auth', 'checkSeller')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'Dashboard']);

    Route::get('/products/create', [SelllerProductController::class, 'createProduct']);
    Route::post('/products/store', [SelllerProductController::class, 'storeProduct'])->name('storeProduct');
    Route::get('/product', [SelllerProductController::class, 'history']);
    Route::get('/testTonKho', [SelllerProductController::class, 'createProduct']);

    Route::get('/testRutTien', [SellerWithDrawController::class, 'Test']);
    Route::get('/TestAds', [SellerAds::class, 'Test']);
    Route::get('/TestDoanhThu', [SellerRevenueController::class, 'Test']);
});

require __DIR__ . '/auth.php';
