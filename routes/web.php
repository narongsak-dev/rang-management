<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
});

require __DIR__.'/auth.php';

