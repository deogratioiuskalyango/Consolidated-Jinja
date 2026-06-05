<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('accountant_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('accountant_id')->nullable()->index();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('property_unit_id')->nullable()->index();
            $table->tinyInteger('category')->default(1);           // EXPENSE_CAT_*
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('UGX');
            $table->tinyInteger('payment_method')->default(1);
            $table->string('vendor_name')->nullable();
            $table->string('vendor_contact')->nullable();
            $table->date('expense_date');
            $table->tinyInteger('status')->default(0);             // EXPENSE_STATUS_*
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            $table->string('receipt_file')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('accountant_expenses'); }
};
