<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('governance_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('title');
            $table->string('reference_number')->nullable();       // MTG-2026-001
            $table->tinyInteger('type')->default(MEETING_TYPE_AGM)->comment('1=AGM,2=Board,3=EGM,4=Other');
            $table->tinyInteger('status')->default(MEETING_STATUS_SCHEDULED);
            $table->text('agenda')->nullable();
            $table->text('minutes')->nullable();
            $table->string('venue')->nullable();
            $table->string('virtual_link')->nullable();          // Zoom/Teams link
            $table->timestamp('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->string('recording_url')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('attendance_confirmed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('governance_meetings');
    }
};
