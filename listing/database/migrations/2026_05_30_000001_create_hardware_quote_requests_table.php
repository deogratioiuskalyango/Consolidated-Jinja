<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('hardware_quote_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 120);
            $table->string('phone', 40)->nullable();
            $table->string('project_type', 120)->nullable();
            $table->string('delivery_location', 180)->nullable();
            $table->date('needed_by')->nullable();
            $table->text('materials');
            $table->string('budget', 120)->nullable();
            $table->string('status', 40)->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hardware_quote_requests');
    }
};
