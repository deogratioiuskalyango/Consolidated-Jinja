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
        if (!Schema::hasColumn('users', 'otp')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('otp')->nullable()->after('verify_token');
            });
        }

        if (!Schema::hasColumn('users', 'otp_expire')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dateTime('otp_expire')->nullable()->after('otp');
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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'otp_expire')) {
                $table->dropColumn('otp_expire');
            }
            if (Schema::hasColumn('users', 'otp')) {
                $table->dropColumn('otp');
            }
        });
    }
};
