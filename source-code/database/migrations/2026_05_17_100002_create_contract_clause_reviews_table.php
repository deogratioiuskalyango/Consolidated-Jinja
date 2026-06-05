<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_clause_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('contract_id');
            $table->string('clause_key', 50);
            $table->enum('status', ['pending', 'agreed', 'disputed'])->default('pending');
            $table->text('tenant_comment')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->foreign('contract_id')
                  ->references('id')
                  ->on('tenancy_contracts')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_clause_reviews');
    }
};
