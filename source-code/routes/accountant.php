<?php

use App\Http\Controllers\Accountant\DashboardController;
use App\Http\Controllers\Accountant\RentCollectionController;
use App\Http\Controllers\Accountant\ExpenseController;
use App\Http\Controllers\Accountant\ReportController;
use App\Http\Controllers\Accountant\ReconciliationController;
use App\Http\Controllers\Accountant\AuditLogController;
use App\Http\Controllers\Accountant\ProfileController as AccountantProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'accountant', 'as' => 'accountant.', 'middleware' => ['auth', 'accountant']], function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Rent Collections
    Route::group(['prefix' => 'collections', 'as' => 'collections.'], function () {
        Route::get('/', [RentCollectionController::class, 'index'])->name('index');
        Route::get('create', [RentCollectionController::class, 'create'])->name('create');
        Route::post('store', [RentCollectionController::class, 'store'])->name('store');
        Route::get('{collection}', [RentCollectionController::class, 'show'])->name('show');
        Route::get('{collection}/receipt', [RentCollectionController::class, 'receipt'])->name('receipt');
        Route::post('{collection}/reverse', [RentCollectionController::class, 'reverse'])->name('reverse');
        Route::get('data', [RentCollectionController::class, 'getData'])->name('data');
    });

    // Expenses
    Route::group(['prefix' => 'expenses', 'as' => 'expenses.'], function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::post('store', [ExpenseController::class, 'store'])->name('store');
        Route::get('{expense}', [ExpenseController::class, 'show'])->name('show');
        Route::post('{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
        Route::post('{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');
        Route::delete('{expense}', [ExpenseController::class, 'destroy'])->name('destroy');
    });

    // Reports
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::post('generate', [ReportController::class, 'generate'])->name('generate');
        Route::get('{report}', [ReportController::class, 'show'])->name('show');
        Route::get('{report}/print', [ReportController::class, 'print'])->name('print');
        Route::get('{report}/export-csv', [ReportController::class, 'exportCsv'])->name('export-csv');
        Route::post('{report}/share', [ReportController::class, 'shareToShareholders'])->name('share');
        Route::delete('{report}', [ReportController::class, 'destroy'])->name('destroy');
    });

    // Reconciliation
    Route::group(['prefix' => 'reconciliation', 'as' => 'reconciliation.'], function () {
        Route::get('/', [ReconciliationController::class, 'index'])->name('index');
        Route::post('upload', [ReconciliationController::class, 'upload'])->name('upload');
        Route::post('{log}/match', [ReconciliationController::class, 'match'])->name('match');
        Route::post('{log}/flag', [ReconciliationController::class, 'flag'])->name('flag');
    });

    // Audit Logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');

    // Tenant Balances
    Route::get('balances', [RentCollectionController::class, 'balances'])->name('balances');

    // Profile
    Route::get('profile', [AccountantProfileController::class, 'index'])->name('profile');
    Route::put('profile/update', [AccountantProfileController::class, 'update'])->name('profile.update');
    Route::get('change-password', [AccountantProfileController::class, 'changePassword'])->name('change-password');
    Route::post('change-password', [AccountantProfileController::class, 'changePasswordUpdate'])->name('change-password.update');
});
