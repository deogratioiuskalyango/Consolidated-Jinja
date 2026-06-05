<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id');
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('role_slug', 50)->nullable();
            $table->string('title', 255);
            $table->text('message');
            $table->string('type', 20)->default('info'); // info, success, warning, danger
            $table->string('icon', 50)->nullable();
            $table->string('action_url', 500)->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_broadcast')->default(false);
            $table->timestamps();
            $table->index(['owner_user_id', 'role_slug']);
            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
    }
};
