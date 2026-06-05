<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('share_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('from_shareholder_id')->index();
            $table->unsignedBigInteger('to_shareholder_id')->nullable()->index(); // null = new buyer
            $table->string('to_name')->nullable();                  // if transferring to non-shareholder
            $table->string('to_email')->nullable();
            $table->decimal('shares', 20, 4);
            $table->decimal('price_per_share', 15, 4)->nullable();
            $table->decimal('total_value', 20, 4)->nullable();
            $table->string('currency', 10)->default('UGX');
            $table->tinyInteger('status')->default(TRANSFER_STATUS_PENDING);
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('legal_document_file_id')->nullable()->index();
            $table->unsignedBigInteger('approved_by')->nullable();  // admin user_id
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('from_shareholder_id')->references('id')->on('shareholders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_transfers');
    }
};
