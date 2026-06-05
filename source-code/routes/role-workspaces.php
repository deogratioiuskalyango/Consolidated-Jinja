<?php

use App\Http\Controllers\RoleWorkspaces\RoleWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'role-workspaces', 'as' => 'role.', 'middleware' => ['auth', 'role.workspace']], function () {
    Route::get('director/dashboard', [RoleWorkspaceController::class, 'dashboard'])->defaults('role', 'director')->name('director.dashboard');
    Route::get('finance-manager/dashboard', [RoleWorkspaceController::class, 'dashboard'])->defaults('role', 'finance_manager')->name('finance-manager.dashboard');
    Route::get('landlord/dashboard', [RoleWorkspaceController::class, 'dashboard'])->defaults('role', 'landlord')->name('landlord.dashboard');
    Route::get('tenant-manager/dashboard', [RoleWorkspaceController::class, 'dashboard'])->defaults('role', 'tenant_manager')->name('tenant-manager.dashboard');
    Route::get('auditor/dashboard', [RoleWorkspaceController::class, 'dashboard'])->defaults('role', 'auditor')->name('auditor.dashboard');
    Route::get('compliance-officer/dashboard', [RoleWorkspaceController::class, 'dashboard'])->defaults('role', 'compliance_officer')->name('compliance-officer.dashboard');
    Route::get('secretary/dashboard', [RoleWorkspaceController::class, 'dashboard'])->defaults('role', 'secretary')->name('secretary.dashboard');

    Route::get('{role}/{section}', [RoleWorkspaceController::class, 'workbench'])
        ->where('role', 'director|finance_manager|landlord|tenant_manager|auditor|compliance_officer|secretary')
        ->name('workbench');
});
