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
        if (!Schema::hasColumn('tickets', 'priority')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->tinyInteger('priority')->default(2)->after('topic_id')
                    ->comment('1=low,2=medium,3=high,4=urgent');
            });
        }

        if (!Schema::hasColumn('ticket_topics', 'category')) {
            Schema::table('ticket_topics', function (Blueprint $table) {
                $table->string('category')->nullable()->after('name');
            });
        }
    }

    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'priority')) {
                $table->dropColumn('priority');
            }
        });
        Schema::table('ticket_topics', function (Blueprint $table) {
            if (Schema::hasColumn('ticket_topics', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
