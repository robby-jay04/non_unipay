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

// Landing / Login
Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.submit');

// Payment Webhook (NO AUTH)
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

// Payment Success / Failed Pages
Route::get('/payments/success', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payments/failed', [PaymentController::class, 'failed'])->name('payment.failed');
Route::get('/payment/success', function () {
    return redirect()->route('payment.success');
});

// Password Reset
Route::get('/password/reset/{token}', [PasswordResetController::class, 'redirectToMobile'])
    ->name('password.reset.mobile');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {

        // DASHBOARD
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // PAYMENTS
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('/payments/export', [AdminController::class, 'exportPayments'])->name('payments.export');
        Route::get('/api/pending-payments-count', [DashboardController::class, 'pendingPaymentsCount'])
            ->name('payments.pending-count');

        // STUDENTS
        Route::get('/students', [AdminController::class, 'students'])->name('students');
        Route::get('/students/{student}/json', [AdminController::class, 'studentJson'])->name('students.json');
        Route::post('/students/{student}/confirm', [AdminController::class, 'confirmStudent'])->name('students.confirm');
        Route::delete('/students/{student}', [AdminController::class, 'destroy'])->name('students.destroy');
        Route::get('/api/new-students-count', [AdminController::class, 'newStudentsCount'])->name('students.new-count');

        // REPORTS
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
        Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
        Route::get('/reports/clearances', [ReportController::class, 'clearances'])->name('reports.clearances');

        // SCHOOL YEARS
        Route::get('/school-years', [SchoolYearController::class, 'index'])->name('school-years.index');
        Route::post('/school-years', [SchoolYearController::class, 'store'])->name('school-years.store');
        Route::post('/school-years/{schoolYear}/set-current', [SchoolYearController::class, 'setCurrent'])->name('school-years.setCurrent');
        Route::post('/school-years/{schoolYear}/set-semester', [SchoolYearController::class, 'setSemester'])->name('school-years.setSemester');
        Route::delete('/school-years/{id}', [SchoolYearController::class, 'destroy'])->name('school-years.destroy');

        // FEE MANAGEMENT
        Route::get('/fees', [FeeController::class, 'adminIndex'])->name('fees.index');
        Route::get('/fees/create', [FeeController::class, 'create'])->name('fees.create');
        Route::post('/fees', [FeeController::class, 'storeWeb'])->name('fees.store');
        Route::get('/fees/{fee}/edit', [FeeController::class, 'edit'])->name('fees.edit');
        Route::put('/fees/{fee}', [FeeController::class, 'updateWeb'])->name('fees.update');
        Route::delete('/fees/{fee}', [FeeController::class, 'destroyWeb'])->name('fees.destroy');

        // ✅ Dynamic semester loader for fee edit form
        Route::get('/api/semesters/{schoolYearId}', function ($schoolYearId) {
            $semesters = \App\Models\Semester::where('school_year_id', $schoolYearId)
                            ->get(['id', 'name']);
            return response()->json($semesters);
        })->name('api.semesters');
    });
});