<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dividend_declarations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('declared_by')->index();
            $table->string('reference_number')->nullable();        // DIV-2026-001
            $table->string('title');
            $table->decimal('total_amount', 20, 4);
            $table->decimal('per_share_amount', 15, 8);            // amount per single share
            $table->string('currency', 10)->default('UGX');
            $table->tinyInteger('status')->default(DIVIDEND_STATUS_DECLARED);
            $table->date('declaration_date');
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('resolution_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dividend_declarations');
    }
};
