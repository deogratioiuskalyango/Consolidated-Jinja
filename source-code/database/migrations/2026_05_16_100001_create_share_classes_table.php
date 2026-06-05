<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('share_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->index();
            $table->string('name');                          // Ordinary, Preference, Deferred
            $table->tinyInteger('type')->default(SHARE_CLASS_ORDINARY);
            $table->text('description')->nullable();
            $table->decimal('par_value', 15, 4)->default(0);// Nominal value per share
            $table->boolean('voting_rights')->default(true);
            $table->decimal('voting_weight', 8, 4)->default(1.0000); // weight per share
            $table->boolean('dividend_rights')->default(true);
            $table->tinyInteger('status')->default(ACTIVE);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_classes');
    }
};
