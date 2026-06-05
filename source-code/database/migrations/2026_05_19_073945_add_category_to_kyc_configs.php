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
        if (!Schema::hasColumn('kyc_configs', 'category')) {
            Schema::table('kyc_configs', function (Blueprint $table) {
                $table->string('category')->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        Schema::table('kyc_configs', function (Blueprint $table) {
            if (Schema::hasColumn('kyc_configs', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
