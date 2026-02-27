<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Exports\PaymentsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\PaymentController;

// ----------------------------
// Public login page (landing page)
// ----------------------------
Route::get('/', [AuthController::class, 'showLoginForm']); // landing page
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login'); // GET login page
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.submit'); // POST login form

// Public webhook (no auth)
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

// Success / Failed pages
Route::get('/payment/success', function () {
    return view('payments.success');
});

Route::get('/payment/failed', function () {
    return view('payments.failed');
});


// ----------------------------
// Protected routes
// ----------------------------
Route::middleware(['auth'])->group(function () {

    // Logout route (POST only)
    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

    // ----------------------------
    // Admin routes
    // ----------------------------
 Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::get('/payments', [PaymentController::class, 'index'])
        ->name('admin.payments');

    Route::get('/payments/{payment}', [PaymentController::class, 'show'])
        ->name('admin.payments.show');

    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])
        ->name('admin.payments.verify');

    Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])
        ->name('admin.payments.reject');

    Route::get('/payments/export', [AdminController::class, 'exportPayments'])
        ->name('admin.payments.export');

    Route::get('/students', [AdminController::class, 'students'])
        ->name('admin.students');

    Route::get('/students/{student}/json', [AdminController::class, 'studentJson'])
        ->name('admin.students.json');

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('admin.reports');
        Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])
    ->name('admin.reports.pdf');
    Route::get('/reports/excel', [ReportController::class, 'exportExcel'])
    ->name('admin.reports.excel');
       Route::get('/reports/clearances', [ReportController::class, 'clearances'])
        ->name('admin.reports.clearances');

        Route::post('/students/{student}/confirm',
    [AdminController::class, 'confirmStudent']
)->name('admin.students.confirm');

Route::delete('/students/{student}', [AdminController::class, 'destroy'])
    ->name('admin.students.destroy');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/students/{student}/json', [AdminController::class, 'studentJson'])
         ->name('admin.students.json');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])
         ->name('admin.payments.show');
});

});
