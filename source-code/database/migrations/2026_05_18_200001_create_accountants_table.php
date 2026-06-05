<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accountants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique()->index();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->string('accountant_id')->unique()->nullable();
            $table->string('license_number')->nullable();
            $table->string('designation')->nullable()->default('Accountant');
            $table->tinyInteger('status')->default(1);
            $table->boolean('force_password_change')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void { Schema::dropIfExists('accountants'); }
};
