<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shareholder_login_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shareholder_id')->index();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_info')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();

            $table->foreign('shareholder_id')->references('id')->on('shareholders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shareholder_login_devices');
    }
};
