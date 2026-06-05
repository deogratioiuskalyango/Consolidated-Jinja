<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_approval_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financial_approval_id')->index();
            $table->unsignedBigInteger('shareholder_id')->index();
            $table->tinyInteger('action')->comment('1=approve, 2=reject');
            $table->text('comment')->nullable();
            $table->decimal('voting_weight', 20, 4)->default(0);   // ownership % at time of action
            $table->string('ip_address')->nullable();
            $table->string('device_info')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();

            $table->unique(['financial_approval_id', 'shareholder_id'], 'fa_actions_unique');
            $table->foreign('financial_approval_id', 'fk_fa_actions_fa')->references('id')->on('financial_approvals')->onDelete('cascade');
            $table->foreign('shareholder_id')->references('id')->on('shareholders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_approval_actions');
    }
};
