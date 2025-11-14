<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseTypeController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleItemController;
use App\Http\Controllers\InventoryTransactionController;

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
    return redirect()->route('dashboard');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Types
    Route::resource('user-types', UserTypeController::class)->except(['show']);

    // Users
    Route::resource('users', UserController::class);

    // Warehouse Types
    Route::resource('warehouse-types', WarehouseTypeController::class)->except(['show']);

    // Products
    Route::resource('products', ProductController::class);
    Route::get('products/data', [ProductController::class, 'getDataTable'])->name('products.data');
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');

    // Warehouses
    Route::resource('warehouses', WarehouseController::class);

    // Product Categories
    Route::resource('product-categories', ProductCategoryController::class)->except(['show']);

    // Inventory
    Route::get('inventories/low-stock', [InventoryController::class, 'lowStock'])->name('inventories.low-stock');
    Route::resource('inventories', InventoryController::class);

    // Transaction Types
    Route::resource('transaction-types', TransactionTypeController::class)->except(['show']);

    // Sales
    Route::get('sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::resource('sales', SaleController::class);
    
    // Sale Items
    Route::resource('sale-items', SaleItemController::class)->except(['index', 'create', 'show']);
    
    // Inventory Transactions
    Route::resource('inventory-transactions', InventoryTransactionController::class)->except(['create', 'edit']);
});

// Authentication routes
require __DIR__.'/auth.php';
