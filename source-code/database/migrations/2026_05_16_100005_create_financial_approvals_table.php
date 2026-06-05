<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('requested_by')->index();   // user_id of requester
            $table->string('reference_number')->nullable();        // FA-2026-001
            $table->string('title');
            $table->string('department')->nullable();
            $table->decimal('amount', 20, 4);
            $table->string('currency', 10)->default('UGX');
            $table->string('payment_category')->nullable();        // e.g. Capital, Operating
            $table->tinyInteger('priority')->default(2)->comment('1=urgent, 2=normal, 3=low');
            $table->text('description');
            $table->text('financial_impact')->nullable();
            $table->tinyInteger('status')->default(APPROVAL_STATUS_PENDING);
            $table->decimal('approval_threshold', 5, 2)->default(51.00); // % of shares needed
            $table->integer('quorum_count')->default(1);           // min shareholders required
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->unsignedBigInteger('expense_id')->nullable()->index(); // link to existing expenses
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_approvals');
    }
};
