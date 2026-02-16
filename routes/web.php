<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductVariantController; // Thêm controller biến thể

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Trang chủ (Khách hàng)
Route::get('/', function () {
    return redirect()->route('admin.login');
})->name('login');

// 2. Nhóm Route dành cho KHÁCH (Chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
});

// 3. Nhóm Route dành cho ADMIN (Bắt buộc phải đăng nhập)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý tài nguyên (CRUD)
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);

    // --- Quản lý Biến thể (Variants) ---
    // (Đã gộp vào đây để được bảo vệ bởi middleware auth)
    Route::prefix('products/{id}/variants')->name('products.variants.')->group(function () {
        Route::get('/', [ProductVariantController::class, 'index'])->name('index');
        Route::post('/', [ProductVariantController::class, 'store'])->name('store');
        
        // Sửa variant (Lưu ý: Route này thường dùng variant_id chứ không cần product_id ở prefix nếu không cần thiết, 
        // nhưng để như bạn cũ cũng được, tôi tinh chỉnh lại cho chuẩn RESTful hơn chút)
        Route::get('/{variant_id}/edit', [ProductVariantController::class, 'edit'])->name('edit');
        Route::put('/{variant_id}', [ProductVariantController::class, 'update'])->name('update');
        Route::delete('/{variant_id}', [ProductVariantController::class, 'destroy'])->name('destroy');
    });
});