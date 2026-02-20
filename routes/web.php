<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSerialController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customers (cashier + admin)
    Route::middleware('role:cashier|admin')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::get('customers/search/api', [CustomerController::class, 'search'])->name('customers.search');

        // Memberships
        Route::get('memberships', [MembershipController::class, 'index'])->name('memberships.index');
        Route::get('memberships/create', [MembershipController::class, 'create'])->name('memberships.create');
        Route::post('memberships', [MembershipController::class, 'store'])->name('memberships.store');
        Route::post('memberships/{membership}/renew', [MembershipController::class, 'renew'])->name('memberships.renew');

        // POS
        Route::get('pos', [POSController::class, 'index'])->name('pos.index');
        Route::get('pos/cart', [POSController::class, 'cart'])->name('pos.cart');
        Route::post('pos/cart/add', [POSController::class, 'cartAdd'])->name('pos.cart.add');
        Route::post('pos/cart/update', [POSController::class, 'cartUpdate'])->name('pos.cart.update');
        Route::post('pos/cart/remove', [POSController::class, 'cartRemove'])->name('pos.cart.remove');
        Route::post('pos/cart/serials', [POSController::class, 'cartSerials'])->name('pos.cart.serials');
        Route::post('pos/customer/set', [POSController::class, 'setCustomer'])->name('pos.customer.set');
        Route::post('pos/customer/clear', [POSController::class, 'clearCustomer'])->name('pos.customer.clear');
        Route::post('pos/scan', [POSController::class, 'scanProduct'])->name('pos.scan');
        Route::post('pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
        Route::get('pos/receipt/{sale}', [POSController::class, 'receipt'])->name('pos.receipt');

        // Rentals
        Route::get('rentals', [RentalController::class, 'index'])->name('rentals.index');
        Route::get('rentals/{rental}', [RentalController::class, 'show'])->name('rentals.show');
        Route::get('rentals/{rental}/return', [RentalController::class, 'returnForm'])->name('rentals.return');
        Route::post('rentals/{rental}/return', [RentalController::class, 'processReturn'])->name('rentals.process-return');

        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    });

    // Products (inventory + admin can create/edit; cashier can view)
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::middleware('role:inventory|admin')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::post('products/{product}/stock-in', [ProductController::class, 'stockIn'])->name('products.stock-in');
        Route::post('products/{product}/adjust', [ProductController::class, 'stockAdjust'])->name('products.adjust');
        Route::get('reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    });

    // Product Serials (inventory + admin)
    Route::middleware('role:inventory|admin')->group(function () {
        Route::get('product-serials', [ProductSerialController::class, 'index'])->name('product-serials.index');
        Route::get('product-serials/create', [ProductSerialController::class, 'create'])->name('product-serials.create');
        Route::post('product-serials', [ProductSerialController::class, 'store'])->name('product-serials.store');
        Route::get('product-serials/{productSerial}/edit', [ProductSerialController::class, 'edit'])->name('product-serials.edit');
        Route::put('product-serials/{productSerial}', [ProductSerialController::class, 'update'])->name('product-serials.update');
        Route::get('product-serials/{productSerial}/history', [ProductSerialController::class, 'history'])->name('product-serials.history');
    });

    // Role & Permission management (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');

        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });
});

require __DIR__.'/auth.php';
