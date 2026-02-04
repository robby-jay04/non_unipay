<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;

// ----------------------------
// Public login page (landing page)
// ----------------------------
Route::get('/', [AuthController::class, 'showLoginForm']); // landing page
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); // GET login page
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.submit'); // POST login form

// ----------------------------
// Protected routes
// ----------------------------
Route::middleware(['auth'])->group(function () {

    // Logout route (POST only)
    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

    // ----------------------------
    // Admin routes
    // ----------------------------
    Route::middleware('admin')->prefix('admin')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');

        Route::get('/payments', [AdminController::class, 'payments'])
            ->name('admin.payments');
            

        Route::get('/students', [AdminController::class, 'students'])
            ->name('admin.students');

        Route::get('/reports', [ReportController::class, 'index'])
            ->name('admin.reports');

        Route::get('/reports/pdf', [ReportController::class, 'exportPDF'])
            ->name('admin.reports.pdf');

        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])
            ->name('admin.reports.excel');

        Route::get('/reports/clearances', [ReportController::class, 'clearanceReport'])
            ->name('admin.reports.clearances');
    });// Export Payments
Route::get('/payments/export', [AdminController::class, 'exportPayments'])->name('admin.payments.export');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/students/{student}/json', [AdminController::class, 'studentJson'])
         ->name('admin.students.json');
});


});
