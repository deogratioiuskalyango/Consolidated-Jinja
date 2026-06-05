<?php

use App\Http\Controllers\Shareholder\DashboardController;
use App\Http\Controllers\Shareholder\DocumentController;
use App\Http\Controllers\Shareholder\DividendController;
use App\Http\Controllers\Shareholder\FinancialApprovalController;
use App\Http\Controllers\Shareholder\MeetingController;
use App\Http\Controllers\Shareholder\NotificationController;
use App\Http\Controllers\Shareholder\ProfileController;
use App\Http\Controllers\Shareholder\ShareTransferController;
use App\Http\Controllers\Shareholder\VotingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'shareholder', 'as' => 'shareholder.', 'middleware' => ['auth', 'shareholder']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('change-password', [ProfileController::class, 'changePassword'])->name('change-password');
    Route::post('change-password', [ProfileController::class, 'changePasswordUpdate'])->name('change-password.update');

    // Voting / Resolutions
    Route::get('resolutions', [VotingController::class, 'index'])->name('resolutions.index');
    Route::get('resolutions/{resolution}', [VotingController::class, 'show'])->name('resolutions.show');
    Route::post('resolutions/{resolution}/vote', [VotingController::class, 'vote'])->name('resolutions.vote');

    // Financial Approvals
    Route::get('financial-approvals', [FinancialApprovalController::class, 'index'])->name('financial-approvals.index');
    Route::get('financial-approvals/{approval}', [FinancialApprovalController::class, 'show'])->name('financial-approvals.show');
    Route::post('financial-approvals/{approval}/action', [FinancialApprovalController::class, 'action'])->name('financial-approvals.action');

    // Documents
    Route::get('documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    // Dividends
    Route::get('dividends', [DividendController::class, 'index'])->name('dividends.index');
    Route::get('dividends/{dividend}', [DividendController::class, 'show'])->name('dividends.show');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Meetings
    Route::get('meetings', [MeetingController::class, 'index'])->name('meetings.index');
    Route::get('meetings/{meeting}', [MeetingController::class, 'show'])->name('meetings.show');
    Route::post('meetings/{meeting}/confirm', [MeetingController::class, 'confirm'])->name('meetings.confirm');

    // Share Transfers
    Route::get('share-transfers', [ShareTransferController::class, 'index'])->name('share-transfers.index');
    Route::post('share-transfers/request', [ShareTransferController::class, 'store'])->name('share-transfers.store');

    // Governance Rights
    Route::get('governance-rights', [DashboardController::class, 'governanceRights'])->name('governance-rights');
});
