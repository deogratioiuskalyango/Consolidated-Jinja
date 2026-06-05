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
        if (!Schema::hasColumn('maintenance_issues', 'category')) {
            Schema::table('maintenance_issues', function (Blueprint $table) {
                $table->string('category')->nullable()->after('name');
            });
        }

        if (!Schema::hasColumn('maintenance_requests', 'priority')) {
            Schema::table('maintenance_requests', function (Blueprint $table) {
                $table->tinyInteger('priority')->default(2)->after('issue_id');
            });
        }
    }

    public function down()
    {
        Schema::table('maintenance_issues', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_issues', 'category')) {
                $table->dropColumn('category');
            }
        });
        Schema::table('maintenance_requests', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_requests', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};
