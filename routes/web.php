<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\PasswordResetController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing / Login
Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.submit');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])
    ->name('password.reset');

// Payment Webhook (NO AUTH)
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

// Payment Success / Failed Pages
Route::get('/payment/success', function () {
    return view('payments.success');
});

Route::get('/payment/failed', function () {
    return view('payments.failed');
});
// Clickable link from email redirects to mobile deep link
Route::get('/password/reset/{token}', [PasswordResetController::class, 'redirectToMobile']);
// Password reset form
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');

// Form submission
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.update');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logoutWeb'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware(['admin'])->prefix('admin')->group(function () {

        // ==========================
        // DASHBOARD
        // ==========================
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('admin.dashboard');


        // ==========================
        // PAYMENTS
        // ==========================
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


        // ==========================
        // STUDENTS
        // ==========================
        Route::get('/students', [AdminController::class, 'students'])
            ->name('admin.students');

        Route::get('/students/{student}/json', [AdminController::class, 'studentJson'])
            ->name('admin.students.json');

        Route::post('/students/{student}/confirm', [AdminController::class, 'confirmStudent'])
            ->name('admin.students.confirm');

        Route::delete('/students/{student}', [AdminController::class, 'destroy'])
            ->name('admin.students.destroy');


        // ==========================
        // REPORTS
        // ==========================
        Route::get('/reports', [ReportController::class, 'index'])
            ->name('admin.reports');

        Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])
            ->name('admin.reports.pdf');

        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])
            ->name('admin.reports.excel');

        Route::get('/reports/clearances', [ReportController::class, 'clearances'])
            ->name('admin.reports.clearances');


        // ==========================
        // SCHOOL YEARS
        // ==========================
        Route::get('/school-years', [SchoolYearController::class, 'index'])
            ->name('admin.school-years.index');

        Route::post('/school-years', [SchoolYearController::class, 'store'])
            ->name('admin.school-years.store');

        Route::post('/school-years/{id}/set-current', [SchoolYearController::class, 'setCurrent'])
            ->name('admin.school-years.setCurrent');


        // ==========================
        // FEE MANAGEMENT
        // ==========================
        // FEE MANAGEMENT
Route::get('/fees', [FeeController::class, 'adminIndex'])
    ->name('admin.fees.index');

Route::get('/fees/create', [FeeController::class, 'create'])
    ->name('admin.fees.create');

Route::post('/fees', [FeeController::class, 'storeWeb'])
    ->name('admin.fees.store');

Route::get('/fees/{fee}/edit', [FeeController::class, 'edit'])
    ->name('admin.fees.edit');

Route::put('/fees/{fee}', [FeeController::class, 'updateWeb'])
    ->name('admin.fees.update');

Route::delete('/fees/{fee}', [FeeController::class, 'destroyWeb'])
    ->name('admin.fees.destroy');
    });
});