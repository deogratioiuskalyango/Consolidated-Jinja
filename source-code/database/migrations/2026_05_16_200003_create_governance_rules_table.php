<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('governance_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->string('rule_key', 100);
            $table->string('rule_name');
            $table->string('rule_type', 50)->default('general');
            $table->text('description')->nullable();
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['owner_user_id', 'rule_key'], 'gov_rules_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('governance_rules');
    }
};
