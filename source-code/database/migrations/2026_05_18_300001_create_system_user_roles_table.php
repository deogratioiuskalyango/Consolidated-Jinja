<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_user_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('role_slug', 50);
            $table->unsignedBigInteger('owner_user_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('removed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->unique(['user_id', 'role_slug', 'owner_user_id'], 'system_user_roles_user_slug_owner_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_user_roles');
    }
};
