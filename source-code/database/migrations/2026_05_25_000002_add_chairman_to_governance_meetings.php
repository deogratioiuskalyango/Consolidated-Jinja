<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('governance_meetings', 'chairman')) {
            Schema::table('governance_meetings', function (Blueprint $table) {
                $table->string('chairman')->nullable()->after('title');
            });
        }

        if (!Schema::hasColumn('governance_meetings', 'meeting_objectives')) {
            Schema::table('governance_meetings', function (Blueprint $table) {
                $table->string('meeting_objectives')->nullable()->after('agenda');
            });
        }
    }

    public function down(): void
    {
        Schema::table('governance_meetings', function (Blueprint $table) {
            if (Schema::hasColumn('governance_meetings', 'meeting_objectives')) {
                $table->dropColumn('meeting_objectives');
            }
            if (Schema::hasColumn('governance_meetings', 'chairman')) {
                $table->dropColumn('chairman');
            }
        });
    }
};
