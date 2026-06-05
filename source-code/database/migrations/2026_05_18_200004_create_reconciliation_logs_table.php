<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reconciliation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('accountant_id')->nullable()->index();
            $table->tinyInteger('recon_type');                      // RECON_TYPE_*
            $table->string('external_ref')->nullable();             // bank/momo ref
            $table->string('payer_name')->nullable();
            $table->string('payer_phone', 30)->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('UGX');
            $table->date('transaction_date');
            $table->tinyInteger('status')->default(2);              // RECON_STATUS_*
            $table->unsignedBigInteger('matched_collection_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->string('flag_reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('reconciliation_logs'); }
};
