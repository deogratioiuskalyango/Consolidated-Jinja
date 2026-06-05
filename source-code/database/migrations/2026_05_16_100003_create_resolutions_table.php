<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->unsignedBigInteger('created_by')->index();       // admin user_id
            $table->string('title');
            $table->string('reference_number')->nullable();          // e.g. RES-2026-001
            $table->tinyInteger('type')->comment('1=financial,2=board,3=property,4=dividend,5=share_transfer,6=policy,7=director,8=other');
            $table->text('description');
            $table->text('supporting_details')->nullable();
            $table->tinyInteger('status')->default(RESOLUTION_STATUS_DRAFT);
            $table->boolean('anonymous_voting')->default(false);
            $table->decimal('quorum_percentage', 5, 2)->default(51.00); // % of shares required
            $table->decimal('pass_threshold', 5, 2)->default(51.00);    // % FOR votes to pass
            $table->boolean('weighted_voting')->default(true);
            $table->timestamp('voting_opens_at')->nullable();
            $table->timestamp('voting_closes_at')->nullable();
            $table->timestamp('passed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->unsignedBigInteger('meeting_id')->nullable()->index();
            $table->decimal('for_percentage', 8, 4)->default(0)->comment('Computed on close');
            $table->decimal('against_percentage', 8, 4)->default(0);
            $table->decimal('abstain_percentage', 8, 4)->default(0);
            $table->decimal('total_votes_cast', 20, 4)->default(0);  // weighted votes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolutions');
    }
};
