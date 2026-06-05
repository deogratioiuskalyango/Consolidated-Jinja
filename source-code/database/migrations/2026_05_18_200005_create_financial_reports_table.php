<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('generated_by')->index();     // accountant user id
            $table->tinyInteger('report_type');                       // REPORT_TYPE_*
            $table->string('title');
            $table->date('period_from');
            $table->date('period_to');
            $table->json('report_data');                              // cached summary JSON
            $table->tinyInteger('status')->default(0);                // REPORT_STATUS_*
            $table->boolean('shared_with_shareholders')->default(false);
            $table->timestamp('shared_at')->nullable();
            $table->unsignedBigInteger('property_id')->nullable()->index();  // null = all properties
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('financial_reports'); }
};
