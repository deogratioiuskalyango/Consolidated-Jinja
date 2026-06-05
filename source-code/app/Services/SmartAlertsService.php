<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\PropertyUnit;

class SmartAlertsService
{
    public function getAlerts(int $ownerUserId): array
    {
        $alerts = [];

        // Overdue invoices
        $overdueCount = Invoice::where('owner_user_id', $ownerUserId)
            ->where('status', INVOICE_STATUS_UNPAID)
            ->where('created_at', '<', now()->subDays(30))
            ->count();
        if ($overdueCount > 0) {
            $alerts[] = ['type' => 'danger', 'icon' => 'ri-error-warning-line', 'message' => "{$overdueCount} invoices overdue by 30+ days", 'action' => null];
        }

        // Vacant units — a unit is vacant when it has no active tenant
        $totalUnits    = PropertyUnit::join('properties', 'property_units.property_id', '=', 'properties.id')
            ->where('properties.owner_user_id', $ownerUserId)
            ->count();
        $occupiedUnits = Tenant::where('owner_user_id', $ownerUserId)
            ->where('status', TENANT_STATUS_ACTIVE)
            ->whereNotNull('unit_id')
            ->distinct('unit_id')->count('unit_id');
        $vacantCount = max(0, $totalUnits - $occupiedUnits);
        if ($vacantCount > 0) {
            $alerts[] = ['type' => 'warning', 'icon' => 'ri-home-line', 'message' => "{$vacantCount} units currently vacant", 'action' => null];
        }

        // Expiring leases in 30 days
        $expiringCount = Tenant::where('owner_user_id', $ownerUserId)
            ->where('status', TENANT_STATUS_ACTIVE)
            ->whereNotNull('lease_end_date')
            ->whereBetween('lease_end_date', [now(), now()->addDays(30)])
            ->count();
        if ($expiringCount > 0) {
            $alerts[] = ['type' => 'warning', 'icon' => 'ri-calendar-close-line', 'message' => "{$expiringCount} leases expiring within 30 days", 'action' => null];
        }

        if (empty($alerts)) {
            $alerts[] = ['type' => 'success', 'icon' => 'ri-checkbox-circle-line', 'message' => 'All systems operating normally', 'action' => null];
        }

        return $alerts;
    }
}
