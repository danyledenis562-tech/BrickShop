<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog');
Route::get('/search-suggestions', [CatalogController::class, 'suggestions'])->name('catalog.suggestions');
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{product:slug}/reviews', [ProductController::class, 'review'])->middleware('auth')->name('product.review');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product:slug}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{product:slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{product:slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/favorites/{product:slug}', [FavoriteController::class, 'store'])->name('favorites.store');
Route::delete('/favorites/{product:slug}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/thanks/{order}', [CheckoutController::class, 'thanks'])->name('checkout.thanks');

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
    Route::resource('banners', AdminBannerController::class)->except(['show']);
    Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update']);
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
