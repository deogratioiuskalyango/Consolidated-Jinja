<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('governance_meetings', 'mode')) {
            Schema::table('governance_meetings', function (Blueprint $table) {
                $table->tinyInteger('mode')->default(1)->after('status')->comment('1=in-person,2=virtual,3=hybrid');
            });
        }

        if (!Schema::hasColumn('governance_meetings', 'google_meet_space_id')) {
            Schema::table('governance_meetings', function (Blueprint $table) {
                $table->string('google_meet_space_id')->nullable()->after('virtual_link');
            });
        }
    }

    public function down(): void
    {
        Schema::table('governance_meetings', function (Blueprint $table) {
            if (Schema::hasColumn('governance_meetings', 'google_meet_space_id')) {
                $table->dropColumn('google_meet_space_id');
            }
            if (Schema::hasColumn('governance_meetings', 'mode')) {
                $table->dropColumn('mode');
            }
        });
    }
};
