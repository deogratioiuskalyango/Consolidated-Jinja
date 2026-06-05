<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rent_collections', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('accountant_id')->nullable()->index();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('property_unit_id')->nullable()->index();
            $table->unsignedBigInteger('invoice_id')->nullable()->index();
            $table->tinyInteger('collection_type')->default(1);  // COLLECTION_TYPE_*
            $table->tinyInteger('payment_method')->default(1);   // PAYMENT_METHOD_*
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2)->default(0);
            $table->decimal('balance_after', 15, 2)->default(0);
            $table->string('currency', 10)->default('UGX');
            $table->string('transaction_ref')->nullable();         // mobile money ref / bank ref
            $table->string('collected_by')->nullable();            // name of staff who collected
            $table->tinyInteger('status')->default(1);             // COLLECTION_STATUS_*
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->string('reversed_by')->nullable();
            $table->timestamp('reversed_at')->nullable();
            $table->text('reversal_reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('rent_collections'); }
};
