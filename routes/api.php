<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\Api\V1\PurchaseController;
use App\Http\Controllers\Api\V1\PurchaseReturnController;
use App\Http\Controllers\Api\V1\SaleController;
use App\Http\Controllers\Api\V1\WarehouseController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\DamagedStockController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\RoleController;

Route::prefix('v1')->group(function () {

    // Auth Routes
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/forgot-password', [App\Http\Controllers\Api\V1\PasswordResetController::class, 'sendResetLinkEmail']);
    Route::post('auth/reset-password', [App\Http\Controllers\Api\V1\PasswordResetController::class, 'reset']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/user', [AuthController::class, 'user']);
        Route::post('auth/profile', [AuthController::class, 'updateProfile']);

        // Notifications
        Route::get('notifications', [App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
        Route::get('notifications/unread', [App\Http\Controllers\Api\V1\NotificationController::class, 'unread']);
        Route::post('notifications/{id}/read', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);

        // Permissions
        Route::get('permissions', [App\Http\Controllers\Api\V1\PermissionController::class, 'index']);
        Route::post('permissions', [App\Http\Controllers\Api\V1\PermissionController::class, 'store']);
        Route::post('permission-request', [App\Http\Controllers\Api\V1\PermissionRequestController::class, 'store']);

        // Dashboard
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);

        // Resources
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('suppliers', SupplierController::class);
        Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('purchase-returns', PurchaseReturnController::class);
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('stock-transfers', StockTransferController::class);

    // Reports Routes
    Route::get('/reports/sales', [ReportController::class, 'sales']);
    Route::get('/reports/purchases', [ReportController::class, 'purchases']);
    Route::get('/reports/stock', [ReportController::class, 'stock']);
    Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss']);
    
    // Settings Routes
    Route::apiResource('settings', SettingController::class)->only(['index', 'update']);
    Route::post('settings', [SettingController::class, 'update']); // Fix for POST update

    // Expenses
    Route::get('expense-categories', [ExpenseController::class, 'categories']);
    Route::post('expense-categories', [ExpenseController::class, 'storeCategory']);
    Route::apiResource('expenses', ExpenseController::class);
    Route::apiResource('damaged-stocks', DamagedStockController::class);
    Route::apiResource('categories', CategoryController::class);
        Route::apiResource('products', ProductController::class);
        Route::apiResource('invoices', InvoiceController::class);
        Route::apiResource('payments', PaymentController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::get('permissions', [RoleController::class, 'permissions']);
    });
});
