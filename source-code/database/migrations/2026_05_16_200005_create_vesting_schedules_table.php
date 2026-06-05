<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vesting_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shareholder_id')->index();
            $table->decimal('total_shares_granted', 20, 4);
            $table->decimal('shares_vested', 20, 4)->default(0);
            $table->decimal('shares_forfeited', 20, 4)->default(0);
            $table->date('vesting_start_date');
            $table->date('cliff_date')->nullable();
            $table->date('vesting_end_date');
            $table->tinyInteger('vesting_frequency')->default(1);
            $table->decimal('shares_per_period', 20, 4)->default(0);
            $table->boolean('employment_linked')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamp('forfeited_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('shareholder_id', 'fk_vest_sh')->references('id')->on('shareholders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vesting_schedules');
    }
};
