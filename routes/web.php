<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use Illuminate\Foundation\Application;
use App\Http\Middleware\IsAdmin; // <--- ایمپورت میدل‌ور ادمین

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// -----------------------------------------------------------------
// گروه روت‌های امن (فقط کاربران لاگین شده و تایید شده)
// -----------------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {

    // داشبورد اصلی: نمایش لیست سفارشات
    Route::get('/dashboard', [OrderController::class, 'index'])->name('dashboard');

    // مدیریت سفارشات
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::patch('/orders/{order}/toggle-installment', [OrderController::class, 'toggleInstallment'])->name('orders.toggleInstallment');

    // مشتریان
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // محصولات
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});


// -----------------------------------------------------------------
// روت‌های مخصوص ادمین (نیازمند لاگین + بودن ادمین)
// -----------------------------------------------------------------
Route::middleware(['auth', IsAdmin::class]) // <--- استفاده تمیز از میدل‌ور کلاس
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::patch('/users/{user}/toggle-activation', [AdminController::class, 'toggleActivation'])->name('users.toggle-activation');
    });

require __DIR__ . '/auth.php';
