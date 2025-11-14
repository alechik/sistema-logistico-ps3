<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\WarehouseController as ApiWarehouseController;
// Remove the API namespace for now to avoid missing controller errors
// We'll add these back once the controllers are created

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (authentication, etc.)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API version 1 routes
Route::prefix('v1')->group(function () {
    // User Types
    Route::apiResource('user-types', UserTypeController::class);
    Route::get('user-types/active/list', [UserTypeController::class, 'activeList']);

    // Users
    Route::apiResource('users', UserController::class);
    
    // Warehouse Types
    Route::apiResource('warehouse-types', WarehouseTypeController::class);
    Route::get('warehouse-types/active/list', [WarehouseTypeController::class, 'activeList']);
    
    // Warehouses
    Route::apiResource('warehouses', WarehouseController::class);
    
    // Product Categories
    Route::apiResource('product-categories', ProductCategoryController::class);
    Route::get('product-categories/active/list', [ProductCategoryController::class, 'activeList']);
    
    // Products
    Route::apiResource('products', ProductController::class);
    Route::get('products/category/{categoryId}', [ProductController::class, 'byCategory']);
    Route::get('products/low-stock', [ProductController::class, 'lowStock']);
    
    // Inventories
    Route::apiResource('inventories', InventoryController::class);
    Route::get('inventories/product/{productId}', [InventoryController::class, 'byProduct']);
    Route::get('inventories/warehouse/{warehouseId}', [InventoryController::class, 'byWarehouse']);
    Route::get('inventories/low-stock', [InventoryController::class, 'lowStock']);
    Route::post('inventories/{inventory}/update-stock', [InventoryController::class, 'updateStock']);
    
    // Transaction Types
    Route::apiResource('transaction-types', TransactionTypeController::class);
    Route::get('transaction-types/active/list', [TransactionTypeController::class, 'activeList']);
    
    // Sales
    Route::apiResource('sales', SaleController::class);
    Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel']);
    Route::get('sales/date-range', [SaleController::class, 'byDateRange']);
    Route::get('sales/summary', [SaleController::class, 'summary']);
    
    // Sale Items (nested under sales)
    Route::prefix('sales/{sale}')->group(function () {
        Route::apiResource('items', SaleItemController::class)->except(['store', 'update', 'destroy']);
        Route::post('items', [SaleItemController::class, 'store']);
        Route::put('items/{item}', [SaleItemController::class, 'update']);
        Route::delete('items/{item}', [SaleItemController::class, 'destroy']);
    });
    
        // Inventory Transactions
    Route::apiResource('inventory-transactions', InventoryTransactionController::class);
    Route::get('inventory-transactions/summary', [InventoryTransactionController::class, 'getSummary']);
    
    // Warehouse Management
    Route::apiResource('warehouses', WarehouseController::class);
    
    // Reports and Settings
    // These routes are temporarily disabled until the controllers are created
    // Uncomment and implement these routes once the controllers are available
    /*
    Route::prefix('reports')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales']);
        Route::get('/inventory', [ReportController::class, 'inventory']);
        Route::get('/inventory-movements', [ReportController::class, 'inventoryMovements']);
        Route::get('/export/{type}/{format}', [ReportController::class, 'export']);
    });
    
    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index']);
        Route::put('/', [SettingController::class, 'update']);
        Route::put('/{key}', [SettingController::class, 'update']);
        Route::delete('/{key}', [SettingController::class, 'destroy']);
        Route::get('/company', [SettingController::class, 'company']);
        Route::put('/company', [SettingController::class, 'updateCompany']);
        Route::get('/print', [SettingController::class, 'printSettings']);
        Route::put('/print', [SettingController::class, 'updatePrintSettings']);
    });
    */
});
