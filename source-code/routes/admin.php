<?php

use App\Http\Controllers\AddonUpdateController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\GovernanceController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\OwnerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AccountantManagementController;
use App\Http\Controllers\Admin\AccountDeletionRequestController;
use App\Http\Controllers\Admin\ShareholderManagementController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('notification', [DashboardController::class, 'notification'])->name('notification');

    // Account Deletion Requests
    Route::group(['prefix' => 'deletion-requests', 'as' => 'deletion-requests.'], function () {
        Route::get('/', [AccountDeletionRequestController::class, 'index'])->name('index');
        Route::post('approve/{id}', [AccountDeletionRequestController::class, 'approve'])->name('approve');
        Route::post('reject/{id}', [AccountDeletionRequestController::class, 'reject'])->name('reject');
    });

    Route::group(['prefix' => 'owner', 'as' => 'owner.'], function () {
        Route::get('/', [OwnerController::class, 'index'])->name('index');
        Route::get('get-info', [OwnerController::class, 'getInfo'])->name('get.info');
        Route::get('delete/{id}', [OwnerController::class, 'delete'])->name('delete');
        Route::post('store', [OwnerController::class, 'store'])->name('store')->middleware('isDemo');
        Route::post('update', [OwnerController::class, 'update'])->name('update')->middleware('isDemo');
    });

    Route::group(['prefix' => 'language', 'as' => 'language.'], function () {
        Route::get('/', [LanguageController::class, 'index'])->name('index');
        Route::post('store', [LanguageController::class, 'store'])->name('store')->middleware('isDemo');
        Route::post('update/{id}', [LanguageController::class, 'update'])->name('update')->middleware('isDemo');
        Route::delete('delete/{id}', [LanguageController::class, 'delete'])->name('delete');

        Route::get('translate/{id}/{iso_code?}', [LanguageController::class, 'translateLanguage'])->name('translate');
        Route::get('update-translate/{id}', [LanguageController::class, 'updateTranslate'])->name('update.translate');
        Route::post('import', [LanguageController::class, 'import'])->name('import');
    });

    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('general-setting', [SettingController::class, 'generalSetting'])->name('general-setting');
        Route::post('general-settings-update', [SettingController::class, 'generalSettingUpdate'])->name('general-setting.update');
        Route::get('color-setting', [SettingController::class, 'colorSetting'])->name('color-setting');
        Route::get('smtp-setting', [SettingController::class, 'smtpSetting'])->name('smtp.setting');
        Route::get('recaptcha-setting', [SettingController::class, 'recaptchaSetting'])->name('recaptcha.setting');
        Route::get('map-box-setting', [SettingController::class, 'mapBoxSetting'])->name('map-box.setting')->middleware('isDemo');
        Route::post('general-settings-env-update', [SettingController::class, 'generalSettingEnvUpdate'])->name('general-setting-env.update');
        Route::get('whatsapp-setting', [SettingController::class, 'whatsappSetting'])->name('whatsapp.setting');
        Route::get('sms-setting', [SettingController::class, 'smsSetting'])->name('sms.setting');
        Route::get('tenancy-setting', [SettingController::class, 'tenancySetting'])->name('tenancy.setting');
        Route::get('frontend-setting', [SettingController::class, 'frontendSetting'])->name('frontend.setting');
        Route::get('listing-setting', [SettingController::class, 'listingSetting'])->name('listing.setting');
        Route::get('agreement-setting', [SettingController::class, 'agreementSetting'])->name('agreement.setting');
        Route::get('reminder-setting', [SettingController::class, 'reminderSetting'])->name('reminder.setting');
        Route::get('cron-setting', [SettingController::class, 'cronSetting'])->name('cron.setting');

        Route::group(['prefix' => 'currency', 'as' => 'currency.'], function () {
            Route::get('/', [CurrencyController::class, 'index'])->name('index');
            Route::post('store', [CurrencyController::class, 'store'])->name('store');
            Route::put('update/{id}', [CurrencyController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [CurrencyController::class, 'delete'])->name('destroy');
        });

        Route::get('storage-link', [SettingController::class, 'storageLink']);
        Route::get('migrate-seed', [SettingController::class, 'migrateSeed']);
        Route::get('cache-clear', [SettingController::class, 'cacheClear']);
    });

    Route::group(['prefix' => 'mail', 'as' => 'mail.'], function () {
        Route::post('test-send', [MailController::class, 'testSend'])->name('test.send');
    });

    Route::get('test-whatsapp', [\App\Http\Controllers\Admin\SettingController::class, 'testWhatsApp'])->name('setting.whatsapp.test');

    // Addons management
    Route::group(['prefix' => 'addons', 'as' => 'addons.'], function () {
        Route::get('/', [AddonController::class, 'index'])->name('index');
    });

    Route::group(['prefix' => 'addon', 'as' => 'addon.'], function () {
        Route::get('details/{code}', [AddonUpdateController::class, 'addonSaasDetails'])->name('details')->withoutMiddleware(['addon.update']);
        Route::post('store', [AddonUpdateController::class, 'addonSaasFileStore'])->name('store')->withoutMiddleware(['addon.update']);
        Route::post('execute', [AddonUpdateController::class, 'addonSaasFileExecute'])->name('execute')->withoutMiddleware(['addon.update']);
        Route::get('delete/{code}', [AddonUpdateController::class, 'addonSaasFileDelete'])->name('delete')->withoutMiddleware(['addon.update']);
    });

    // ---- Tenancy Contracts ----
    Route::group(['prefix' => 'contracts', 'as' => 'contracts.'], function () {
        Route::get('/', [ContractController::class, 'index'])->name('index');
        Route::get('create', [ContractController::class, 'create'])->name('create');
        Route::post('store', [ContractController::class, 'store'])->name('store');
        Route::get('tenant-info', [ContractController::class, 'tenantInfo'])->name('tenant-info');
        Route::get('{id}', [ContractController::class, 'show'])->name('show');
        Route::post('{id}/send', [ContractController::class, 'send'])->name('send');
        Route::post('{id}/admin-sign', [ContractController::class, 'adminSign'])->name('admin-sign');
        Route::post('{id}/lc1-sign', [ContractController::class, 'lc1Sign'])->name('lc1-sign');
        Route::post('{id}/witness1-sign', [ContractController::class, 'witness1Sign'])->name('witness1-sign');
        Route::delete('{id}', [ContractController::class, 'destroy'])->name('destroy');
    });

    // ---- Shareholder Management ----
    Route::group(['prefix' => 'shareholders', 'as' => 'shareholders.'], function () {
        Route::get('/', [ShareholderManagementController::class, 'index'])->name('index');
        Route::get('data', [ShareholderManagementController::class, 'getData'])->name('data');
        Route::post('store', [ShareholderManagementController::class, 'store'])->name('store');
        Route::get('{shareholder}/roles', [ShareholderManagementController::class, 'roles'])->name('roles');
        Route::post('{shareholder}/roles', [ShareholderManagementController::class, 'updateRoles'])->name('roles.update');
        Route::get('{shareholder}', [ShareholderManagementController::class, 'edit'])->name('edit');
        Route::post('{shareholder}/update', [ShareholderManagementController::class, 'update'])->name('update');
        Route::delete('{shareholder}', [ShareholderManagementController::class, 'destroy'])->name('destroy');
        Route::post('suspend/{shareholder}', [ShareholderManagementController::class, 'suspend'])->name('suspend');
        Route::post('reactivate/{shareholder}', [ShareholderManagementController::class, 'reactivate'])->name('reactivate');
        Route::post('{shareholder}/resend-credentials', [ShareholderManagementController::class, 'resendCredentials'])->name('resend-credentials');
    });

    // ---- Accountant Management ----
    Route::group(['prefix' => 'accountants', 'as' => 'accountants.'], function () {
        Route::get('/', [AccountantManagementController::class, 'index'])->name('index');
        Route::get('data', [AccountantManagementController::class, 'getData'])->name('data');
        Route::post('store', [AccountantManagementController::class, 'store'])->name('store');
        Route::get('{accountant}/edit', [AccountantManagementController::class, 'edit'])->name('edit');
        Route::post('{accountant}/update', [AccountantManagementController::class, 'update'])->name('update');
        Route::post('{accountant}/suspend', [AccountantManagementController::class, 'suspend'])->name('suspend');
        Route::post('{accountant}/reactivate', [AccountantManagementController::class, 'reactivate'])->name('reactivate');
        Route::delete('{accountant}', [AccountantManagementController::class, 'destroy'])->name('destroy');
    });

    // ---- Governance ----
    Route::group(['prefix' => 'governance', 'as' => 'governance.'], function () {

        Route::group(['prefix' => 'resolutions', 'as' => 'resolutions.'], function () {
            Route::get('/', [GovernanceController::class, 'resolutionsIndex'])->name('index');
            Route::post('store', [GovernanceController::class, 'resolutionStore'])->name('store');
            Route::get('{resolution}/edit', [GovernanceController::class, 'resolutionEdit'])->name('edit');
            Route::post('{resolution}/update', [GovernanceController::class, 'resolutionUpdate'])->name('update');
            Route::delete('{resolution}', [GovernanceController::class, 'resolutionDestroy'])->name('destroy');
        });

        Route::group(['prefix' => 'approvals', 'as' => 'approvals.'], function () {
            Route::get('/', [GovernanceController::class, 'approvalsIndex'])->name('index');
            Route::post('store', [GovernanceController::class, 'approvalStore'])->name('store');
        });

        Route::group(['prefix' => 'documents', 'as' => 'documents.'], function () {
            Route::get('/', [GovernanceController::class, 'documentsIndex'])->name('index');
            Route::post('store', [GovernanceController::class, 'documentStore'])->name('store');
            Route::delete('{document}', [GovernanceController::class, 'documentDelete'])->name('delete');
        });

        Route::group(['prefix' => 'dividends', 'as' => 'dividends.'], function () {
            Route::get('/', [GovernanceController::class, 'dividendsIndex'])->name('index');
            Route::post('store', [GovernanceController::class, 'dividendStore'])->name('store');
        });

        Route::group(['prefix' => 'meetings', 'as' => 'meetings.'], function () {
            Route::get('/', [GovernanceController::class, 'meetingsIndex'])->name('index');
            Route::post('store', [GovernanceController::class, 'meetingStore'])->name('store');
            Route::post('bulk', [GovernanceController::class, 'meetingBulkAction'])->name('bulk');
            Route::get('{meeting}', [GovernanceController::class, 'meetingShow'])->name('show');
            Route::post('{meeting}/update', [GovernanceController::class, 'meetingUpdate'])->name('update');
            Route::post('{meeting}/reschedule', [GovernanceController::class, 'meetingReschedule'])->name('reschedule');
            Route::post('{meeting}/delete', [GovernanceController::class, 'meetingDestroy'])->name('destroy');
            Route::post('{meeting}/minutes', [GovernanceController::class, 'meetingUpdateMinutes'])->name('minutes.update');
            Route::post('{meeting}/recording', [GovernanceController::class, 'meetingUpdateRecording'])->name('recording.update');
            Route::post('{meeting}/status', [GovernanceController::class, 'meetingUpdateStatus'])->name('status.update');
            Route::post('{meeting}/document', [GovernanceController::class, 'meetingUploadDocument'])->name('document.upload');
            Route::post('{meeting}/generate-meet', [GovernanceController::class, 'meetingGenerateGoogleMeet'])->name('generate-meet');
        });

        Route::get('audit-logs', [GovernanceController::class, 'auditLogs'])->name('audit-logs');
    });

    // Share Class Management
    Route::group(['prefix' => 'share-classes', 'as' => 'share-classes.'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\ShareClassController::class, 'index'])->name('index');
        Route::get('{shareClass}', [\App\Http\Controllers\Admin\ShareClassController::class, 'show'])->name('show');
        Route::post('{shareClass}/update', [\App\Http\Controllers\Admin\ShareClassController::class, 'update'])->name('update');
        Route::post('{shareClass}/permissions', [\App\Http\Controllers\Admin\ShareClassController::class, 'updatePermissions'])->name('permissions');
    });

    // Governance Rules & Thresholds
    Route::group(['prefix' => 'governance-rules', 'as' => 'governance-rules.'], function () {
        Route::get('/', [\App\Http\Controllers\Admin\GovernanceRulesController::class, 'index'])->name('index');
        Route::post('rule', [\App\Http\Controllers\Admin\GovernanceRulesController::class, 'storeRule'])->name('rule.store');
        Route::post('rule/{rule}/toggle', [\App\Http\Controllers\Admin\GovernanceRulesController::class, 'toggleRule'])->name('rule.toggle');
        Route::post('threshold', [\App\Http\Controllers\Admin\GovernanceRulesController::class, 'storeThreshold'])->name('threshold.store');
        Route::delete('threshold/{threshold}', [\App\Http\Controllers\Admin\GovernanceRulesController::class, 'destroyThreshold'])->name('threshold.destroy');
    });
});
