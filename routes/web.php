<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromoCodeController as AdminPromoCodeController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Payments\LiqPayCallbackController;
use App\Http\Controllers\Payments\TestPaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/health', fn () => response()->json(['ok' => true], 200))->name('health');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::post('/payments/test-webhook', [TestPaymentController::class, 'webhook'])
    ->middleware('throttle:60,1')
    ->name('payments.test.webhook');

Route::post('/payments/liqpay/callback', [LiqPayCallbackController::class, 'callback'])
    ->middleware('throttle:120,1')
    ->name('payments.liqpay.callback');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:15,1')
    ->name('newsletter.subscribe');
Route::get('/media/public/{path}', [MediaController::class, 'publicStorage'])
    ->where('path', '.*')
    ->name('media.public');
Route::get('/media/product-image/{image}', [MediaController::class, 'productImage'])
    ->name('media.product-image');

Route::get('/shipping/nova/cities', [ShippingController::class, 'novaCities'])->name('shipping.nova.cities');
Route::get('/shipping/nova/branches', [ShippingController::class, 'novaBranches'])->name('shipping.nova.branches');
Route::get('/shipping/nova/streets', [ShippingController::class, 'novaStreets'])->name('shipping.nova.streets');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:checkout')->name('checkout.store');
Route::get('/checkout/thanks/{order}', [CheckoutController::class, 'thanks'])->name('checkout.thanks');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/search-suggestions', [CatalogController::class, 'suggestions'])->name('catalog.suggestions');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{product:slug}/reviews', [ProductController::class, 'review'])->middleware(['auth', 'throttle:reviews'])->name('product.review');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product:slug}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{product:slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{product:slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/favorites/{product:slug}', [FavoriteController::class, 'store'])->name('favorites.store');
Route::delete('/favorites/{product:slug}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

Route::middleware('auth')->group(function () {
    Route::post('/checkout/pay-test/{order}', [TestPaymentController::class, 'simulate'])
        ->middleware('throttle:10,1')
        ->name('checkout.pay-test');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');
    Route::get('/profile/recent', [ProfileController::class, 'recent'])->name('profile.recent');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/orders/{order}/cancel', [ProfileController::class, 'cancelOrder'])->name('profile.orders.cancel');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class);
    Route::resource('promo-codes', AdminPromoCodeController::class)->except(['show']);
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
    Route::get('orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
    Route::resource('reviews', AdminReviewController::class)->only(['index', 'update', 'destroy']);
    Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

Route::view('/about', 'pages.about')->name('about');
Route::view('/shipping', 'pages.shipping')->name('shipping');
Route::view('/payment', 'pages.payment')->name('payment');
Route::view('/returns', 'pages.returns')->name('returns');
Route::view('/contacts', 'pages.contacts')->name('contacts');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/policy', 'pages.policy')->name('policy');

require __DIR__.'/auth.php';
