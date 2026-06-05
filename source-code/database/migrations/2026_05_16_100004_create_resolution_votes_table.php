<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resolution_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resolution_id')->index();
            $table->unsignedBigInteger('shareholder_id')->index();
            $table->tinyInteger('vote')->comment('1=for, 2=against, 3=abstain');
            $table->decimal('voting_weight', 20, 4)->default(0);     // shares * weight at time of vote
            $table->text('comment')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device_info')->nullable();
            $table->timestamp('voted_at')->nullable();
            $table->timestamps();

            $table->unique(['resolution_id', 'shareholder_id'], 'res_votes_unique');
            $table->foreign('resolution_id', 'fk_res_votes_res')->references('id')->on('resolutions')->onDelete('cascade');
            $table->foreign('shareholder_id', 'fk_res_votes_sh')->references('id')->on('shareholders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resolution_votes');
    }
};
