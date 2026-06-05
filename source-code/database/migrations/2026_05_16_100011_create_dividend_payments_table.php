<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dividend_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dividend_declaration_id')->index();
            $table->unsignedBigInteger('shareholder_id')->index();
            $table->decimal('shares_at_declaration', 20, 4);       // snapshot of shares held
            $table->decimal('amount', 20, 4);                       // calculated payout
            $table->string('currency', 10)->default('UGX');
            $table->tinyInteger('status')->default(DIVIDEND_STATUS_DECLARED);
            $table->string('payment_method')->nullable();           // bank, mobile_money
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('dividend_declaration_id')->references('id')->on('dividend_declarations')->onDelete('cascade');
            $table->foreign('shareholder_id')->references('id')->on('shareholders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dividend_payments');
    }
};
