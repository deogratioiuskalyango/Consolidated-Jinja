<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('governance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('action');                               // login, vote, approve, etc.
            $table->string('subject_type')->nullable();             // App\Models\Resolution
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_info')->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            // Immutable — no updates/deletes via app
            $table->index(['owner_user_id', 'action']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('governance_audit_logs');
    }
};
