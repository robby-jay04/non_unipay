<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ClearanceController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ExamPeriodController;
use App\Http\Controllers\ChatbotController;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/password/reset-request', [PasswordResetController::class, 'sendResetLink']);
Route::get('/school-years', [App\Http\Controllers\SchoolYearController::class, 'apiIndex']);

// PayMongo Webhook (Public - no auth required)
Route::post('/webhooks/paymongo', [PaymentController::class, 'webhook'])->name('paymongo.webhook');

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/student/profile/picture', [StudentController::class, 'uploadProfilePicture']);

    // Student Routes
    Route::middleware('student')->prefix('student')->group(function () {
        Route::get('/profile', [StudentController::class, 'profile']);
        Route::put('/profile', [StudentController::class, 'updateProfile']);
        Route::get('/payments', [StudentController::class, 'paymentHistory']);
        Route::put('/change-password', [AuthController::class, 'changePassword']);
    });

    // Chatbot Route
    Route::post('/chatbot', [ChatbotController::class, 'chat']);

    // Fee Routes (Both Admin & Student)
    Route::prefix('fees')->group(function () {
        Route::get('/', [FeeController::class, 'index']);
        Route::get('/total', [FeeController::class, 'getTotalFees']);
        Route::get('/breakdown', [FeeController::class, 'breakdown']);
        Route::get('/type/{type}', [FeeController::class, 'getByType']);
        Route::get('/{id}', [FeeController::class, 'show']);

        // Admin only - Fee management
        Route::middleware('admin')->group(function () {
            Route::post('/', [FeeController::class, 'store']);
            Route::put('/{id}', [FeeController::class, 'update']);
            Route::delete('/{id}', [FeeController::class, 'destroy']);
        });
    });

    // Exam Period — both routes point to the same controller method
    // /current-exam-period  → kept for backward compatibility
    // /exam-period/current  → used by HomeScreen
    Route::get('/current-exam-period', [ExamPeriodController::class, 'current']);
    Route::get('/exam-period/current', [ExamPeriodController::class, 'current']); // ← NEW

    // Payment Routes
    Route::prefix('payments')->group(function () {
        Route::post('/initiate', [PaymentController::class, 'initiate']);
        Route::get('/history', [PaymentController::class, 'history']);
        Route::get('/status/{id}', [PaymentController::class, 'status']);
        Route::get('/{id}', [PaymentController::class, 'show']);
        Route::get('/{id}/receipt', [PaymentController::class, 'downloadReceipt']);
    });

    // Clearance Routes
    Route::get('/clearance', [ClearanceController::class, 'show']);
    Route::get('/clearance/{studentId}', [ClearanceController::class, 'checkClearance']);

    // Notification Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::delete('/clear-all', [NotificationController::class, 'clearAll']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'apiStats']);

        // Payments Management
        Route::get('/payments', [PaymentController::class, 'index']);
        Route::get('/payments/{id}', [PaymentController::class, 'show']);

        // Clearance Management
        Route::post('/clearance/{studentId}/update', [ClearanceController::class, 'updateClearance']);

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('/payments', [ReportController::class, 'paymentReport']);
            Route::get('/export/pdf', [ReportController::class, 'exportPDF']);
            Route::get('/export/excel', [ReportController::class, 'exportExcel']);
        });
    });

});