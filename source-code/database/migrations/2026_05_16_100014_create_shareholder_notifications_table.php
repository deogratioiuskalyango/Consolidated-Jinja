<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shareholder_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('shareholder_id')->nullable()->index(); // null = broadcast to all
            $table->string('type');                                 // vote_reminder, approval_request, announcement, dividend, meeting, etc.
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_broadcast')->default(false);        // true = sent to all shareholders
            $table->string('channel')->nullable();                  // in_app, email, sms
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['shareholder_id', 'is_read']);
            $table->foreign('shareholder_id')->references('id')->on('shareholders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shareholder_notifications');
    }
};
