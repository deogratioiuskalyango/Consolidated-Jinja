<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meeting_attendees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id')->index();
            $table->unsignedBigInteger('shareholder_id')->index();
            $table->boolean('invited')->default(true);
            $table->boolean('confirmed')->default(false);
            $table->boolean('attended')->default(false);
            $table->tinyInteger('attendance_mode')->nullable()->comment('1=in-person,2=virtual');
            $table->timestamp('confirmed_at')->nullable();
            $table->text('apology_note')->nullable();
            $table->timestamps();

            $table->unique(['meeting_id', 'shareholder_id'], 'mtg_attendees_unique');
            $table->foreign('meeting_id', 'fk_mtg_attendees_mtg')->references('id')->on('governance_meetings')->onDelete('cascade');
            $table->foreign('shareholder_id', 'fk_mtg_attendees_sh')->references('id')->on('shareholders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_attendees');
    }
};
