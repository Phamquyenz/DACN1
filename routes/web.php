<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [HomeController::class, 'products'])->name('products.index');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');
Route::get('/test-popup', function() {
    $vouchers = \App\Models\Voucher::where('is_for_new_user', true)->get();
    $rewardedVouchers = [];
    foreach($vouchers as $v) {
        $rewardedVouchers[] = [
            'code' => $v->code,
            'type' => $v->type,
            'discount_value' => $v->discount_value
        ];
    }
    session()->flash('rewarded_vouchers', $rewardedVouchers);
    return redirect('/');
});
Route::get('/flash-sale', [HomeController::class, 'flashSale'])->name('flash_sale');
Route::get('/so-do-trang', [HomeController::class, 'sitemap'])->name('sitemap');
Route::get('/sitemap.xml', [HomeController::class, 'sitemapXml'])->name('sitemap.xml');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/voucher/apply', [CartController::class, 'applyVoucher'])->name('cart.voucher.apply');
Route::post('/cart/voucher/remove', [CartController::class, 'removeVoucher'])->name('cart.voucher.remove');
Route::get('/vnpay/return', [OrderController::class, 'vnpayReturn'])->name('vnpay.return');

// Chat Routes for Customer
Route::get('/chat/messages', [ChatController::class, 'getMessages'])->name('chat.messages');
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

Route::get('/dashboard', function () {
    if (auth()->user() && auth()->user()->role === 'admin') {
        return app(\App\Http\Controllers\Admin\DashboardController::class)->index();
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Checkout và Đơn hàng
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    
    // Kho Voucher
    Route::get('/my-vouchers', [App\Http\Controllers\VoucherWalletController::class, 'index'])->name('my-vouchers.index');
    Route::post('/my-vouchers/save', [App\Http\Controllers\VoucherWalletController::class, 'save'])->name('my-vouchers.save');
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/export', [App\Http\Controllers\Admin\DashboardController::class, 'export'])->name('dashboard.export');
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update']);
    Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class);
    Route::get('customers', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customers.index');
    
    // Flash Sale
    Route::get('flash-sale', [App\Http\Controllers\Admin\FlashSaleController::class, 'index'])->name('flash_sale.index');
    Route::get('flash-sale/active', [App\Http\Controllers\Admin\FlashSaleController::class, 'active'])->name('flash_sale.active');
    Route::post('flash-sale/batch-update', [App\Http\Controllers\Admin\FlashSaleController::class, 'batchUpdate'])->name('flash_sale.batch_update');
    Route::post('flash-sale/toggle/{id}', [App\Http\Controllers\Admin\FlashSaleController::class, 'toggle'])->name('flash_sale.toggle');
    Route::post('flash-sale/update/{id}', [App\Http\Controllers\Admin\FlashSaleController::class, 'update'])->name('flash_sale.update');

    Route::get('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('inventory.store');

    // Quản lý liên lạc
    Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{sessionId}', [AdminContactController::class, 'getMessages'])->name('contacts.messages');
    Route::post('contacts/{sessionId}/reply', [AdminContactController::class, 'reply'])->name('contacts.reply');
});


require __DIR__.'/auth.php';
