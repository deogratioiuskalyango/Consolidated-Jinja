<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_delegations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delegator_shareholder_id')->index();
            $table->unsignedBigInteger('delegatee_shareholder_id')->index();
            $table->unsignedBigInteger('resolution_id')->nullable()->index();
            $table->date('valid_from');
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('delegator_shareholder_id', 'fk_vd_delegator')->references('id')->on('shareholders')->onDelete('cascade');
            $table->foreign('delegatee_shareholder_id', 'fk_vd_delegatee')->references('id')->on('shareholders')->onDelete('cascade');
            $table->foreign('resolution_id', 'fk_vd_resolution')->references('id')->on('resolutions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_delegations');
    }
};
