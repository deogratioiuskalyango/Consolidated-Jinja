<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('maintenance_requests', 'created_date')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->date('created_date')->nullable()->after('amount');
            });
        }

        if (!Schema::hasColumn('maintenance_requests', 'resolved_date')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->date('resolved_date')->nullable()->after('created_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('maintenance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_requests', 'resolved_date')) {
                $table->dropColumn('resolved_date');
            }
            if (Schema::hasColumn('maintenance_requests', 'created_date')) {
                $table->dropColumn('created_date');
            }
        });
    }
};
