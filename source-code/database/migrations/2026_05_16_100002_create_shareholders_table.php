<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shareholders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique()->index();
            $table->unsignedBigInteger('owner_user_id')->index();   // which company they belong to
            $table->unsignedBigInteger('share_class_id')->nullable()->index();
            $table->string('shareholder_id')->unique();             // e.g. SH-0001
            $table->string('nid_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->text('address')->nullable();
            $table->decimal('total_shares', 20, 4)->default(0);
            $table->decimal('ownership_percentage', 8, 4)->default(0);
            $table->decimal('share_value', 15, 4)->default(0);     // current market value per share
            $table->date('date_joined')->nullable();
            $table->tinyInteger('status')->default(SHAREHOLDER_STATUS_ACTIVE);
            $table->boolean('force_password_change')->default(true);// first login flag
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->string('voting_rights_type')->default('proportional'); // proportional | fixed | none
            $table->decimal('fixed_voting_weight', 8, 4)->default(1.0000);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('share_class_id')->references('id')->on('share_classes')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shareholders');
    }
};
