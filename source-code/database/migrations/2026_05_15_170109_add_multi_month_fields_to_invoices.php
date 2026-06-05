<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('invoices', 'months_covered')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->tinyInteger('months_covered')->default(1)->after('month');
            });
        }

        if (!Schema::hasColumn('invoice_recurring_settings', 'months_covered')) {
            Schema::table('invoice_recurring_settings', function (Blueprint $table) {
                $table->tinyInteger('months_covered')->default(1)->after('cycle_day');
            });
        }

        if (!Schema::hasColumn('invoice_recurring_settings', 'installments_per_year')) {
            Schema::table('invoice_recurring_settings', function (Blueprint $table) {
                $table->tinyInteger('installments_per_year')->nullable()->after('months_covered')
                    ->comment('For yearly type: number of installment invoices per year (2,3,4,6,12)');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'months_covered')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropColumn('months_covered');
            });
        }

        Schema::table('invoice_recurring_settings', function (Blueprint $table) {
            if (Schema::hasColumn('invoice_recurring_settings', 'installments_per_year')) {
                $table->dropColumn('installments_per_year');
            }
            if (Schema::hasColumn('invoice_recurring_settings', 'months_covered')) {
                $table->dropColumn('months_covered');
            }
        });
    }
};
