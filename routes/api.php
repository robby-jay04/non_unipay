<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\GCashController;
use App\Http\Controllers\ClearanceController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Payment Callback (Public for GCash webhook)
Route::post('/payment/callback', [GCashController::class, 'paymentCallback'])->name('api.payment.webhook');

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Student Routes
    Route::middleware('student')->prefix('student')->group(function () {
        Route::get('/profile', [StudentController::class, 'profile']);
        Route::put('/profile', [StudentController::class, 'updateProfile']);
        Route::get('/payments', [StudentController::class, 'paymentHistory']);
    });

    // Fee Routes (Both Admin & Student)
    Route::get('/fees', [\App\Http\Controllers\FeeController::class, 'index']);
    Route::get('/fees/total', [\App\Http\Controllers\FeeController::class, 'getTotalFees']);
    Route::get('/fees/breakdown', [\App\Http\Controllers\FeeController::class, 'breakdown']);
    // Payment Routes
    Route::prefix('payments')->group(function () {
        Route::post('/initiate', [GCashController::class, 'initiatePayment']);
        Route::get('/history', [PaymentController::class, 'history']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::get('/{referenceNo}/status', [GCashController::class, 'checkStatus']);
    });

    // Clearance Routes
    Route::get('/clearance', [ClearanceController::class, 'show']);
    Route::get('/clearance/{studentId}', [ClearanceController::class, 'checkClearance']);

    // Receipt
    Route::get('/receipt/{id}', [PaymentController::class, 'downloadReceipt']);

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard/stats', [DashboardController::class, 'apiStats']);
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::post('/clearance/{studentId}/update', [ClearanceController::class, 'updateClearance']);
        
        // Reports
        Route::get('/reports/payments', [ReportController::class, 'paymentReport']);
        Route::get('/reports/export/pdf', [ReportController::class, 'exportPDF']);
        Route::get('/reports/export/excel', [ReportController::class, 'exportExcel']);
    });
});
