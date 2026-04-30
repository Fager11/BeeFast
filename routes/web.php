<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\FavoriteController;
use Illuminate\Support\Facades\Route;
use App\Models\Store;
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

Route::get('/', function () {
    $stores = Store::where('popular', 1)
        ->where('type', 'store')
        ->orderBy('id', 'desc')
        ->limit(4)
        ->get();
    $restaurants = Store::where('popular', 1)
        ->where('type', 'restaurant')
        ->orderBy('id', 'desc')
        ->limit(4)
        ->get();
    $cafes = Store::where('popular', 1)
        ->where('type', 'cafe')
        ->orderBy('id', 'desc')
        ->limit(4)
        ->get();

    return view('welcome', [
        'stores' => $stores,
        'restaurants' => $restaurants,
        'cafes' => $cafes,
    ]);
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
    Route::get('/driver/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('driver.dashboard');
});
Route::get('/profile', [ProfileController::class, 'me'])->name('profile');
Route::post('/profile', [ProfileController::class, 'update'])->name('update_profile');
Route::get('/users', [UserController::class, 'indexuser']);
Route::get('/users/{user}/delete',[UserController::class,'destroy']);

//stores products
Route::resource('stores', StoreController::class);
Route::get('/stores-products', [ProductController::class, 'allStoresProducts'])->name('products.allStores');
Route::resource('products', ProductController::class);

//cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

//notification
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
Route::delete('/notifications/clear', [App\Http\Controllers\NotificationController::class, 'clear'])
    ->name('notifications.clear')
    ->middleware('auth');

//order
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{id}/cannot-cancel', [OrderController::class, 'cannotCancel'])->name('orders.cannotCancel');
Route::post('/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
Route::post('/orders/{order}/assign-driver', [OrderController::class, 'assignDriver'])->name('orders.assignDriver');
Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.myOrders');

Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
Route::post('/favorite/toggle/{id}', [FavoriteController::class, 'toggle'])->name('favorite.toggle');
