<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_class_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('share_class_id')->index();
            $table->string('permission_key', 100);
            $table->boolean('is_allowed')->default(false);
            $table->decimal('limit_value', 20, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['share_class_id', 'permission_key'], 'scp_unique');
            $table->foreign('share_class_id', 'fk_scp_sc')->references('id')->on('share_classes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_class_permissions');
    }
};
