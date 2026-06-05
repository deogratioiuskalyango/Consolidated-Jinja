<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_thresholds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->string('name');
            $table->decimal('min_amount', 20, 4)->default(0);
            $table->decimal('max_amount', 20, 4)->nullable();
            $table->json('required_approver_classes');
            $table->tinyInteger('min_approver_count')->default(1);
            $table->decimal('required_ownership_percentage', 8, 4)->nullable();
            $table->boolean('requires_quorum')->default(false);
            $table->decimal('quorum_percentage', 8, 4)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_thresholds');
    }
};
