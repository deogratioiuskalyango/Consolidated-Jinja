<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_balance_ledger', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('property_unit_id')->nullable()->index();
            $table->decimal('total_charged', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);       // total_charged - total_paid
            $table->decimal('security_deposit_held', 15, 2)->default(0);
            $table->decimal('advance_credit', 15, 2)->default(0);
            $table->string('currency', 10)->default('UGX');
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['tenant_id', 'property_unit_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('tenant_balance_ledger'); }
};
