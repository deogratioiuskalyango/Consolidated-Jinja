<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('governance_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('uploaded_by')->index();     // user_id
            $table->string('title');
            $table->string('document_type')->nullable();            // financial_report, agm_report, policy, etc.
            $table->text('description')->nullable();
            $table->unsignedBigInteger('file_id')->nullable()->index(); // FileManager id
            $table->string('version')->default('1.0');
            $table->tinyInteger('status')->default(DOC_STATUS_ACTIVE);
            $table->boolean('requires_signature')->default(false);
            $table->date('expiry_date')->nullable();
            $table->boolean('all_shareholders')->default(true);     // or specific
            $table->unsignedBigInteger('meeting_id')->nullable()->index();
            $table->unsignedBigInteger('resolution_id')->nullable()->index();
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('file_id')->references('id')->on('file_managers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('governance_documents');
    }
};
