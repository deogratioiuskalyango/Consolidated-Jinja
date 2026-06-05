<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenancy_contracts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('unit_id')->nullable();

            $table->string('reference_no', 50)->unique();
            $table->enum('status', [
                'draft',
                'sent',
                'reviewing',
                'disputed',
                'pending_signature',
                'signed',
                'active',
                'terminated',
            ])->default('draft');

            // Section A – Tenant Particulars
            $table->string('tenant_name');
            $table->string('tenant_phone')->nullable();
            $table->string('tenant_email')->nullable();
            $table->string('tenant_nationality')->nullable();
            $table->string('tenant_id_type', 50)->nullable();
            $table->string('tenant_id_number', 100)->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_phone')->nullable();

            // Section B – Property Details
            $table->string('property_name');
            $table->string('property_address')->nullable();
            $table->string('unit_name')->nullable();

            // Section C – Payment Terms
            $table->decimal('monthly_rent', 15, 2);
            $table->decimal('security_deposit', 15, 2);
            $table->string('currency', 10)->default('UGX');
            $table->unsignedTinyInteger('payment_due_day')->default(1);
            $table->string('payment_method')->nullable();

            // Section D – Bank Details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('mobile_money_number')->nullable();

            // Section E – Period
            $table->date('commencement_date');
            $table->date('expiry_date');
            $table->unsignedInteger('notice_period_days')->default(30);

            // Contract text / clauses
            $table->text('special_conditions')->nullable();

            // Signature fields (base64 PNG stored as LONGTEXT)
            $table->longText('tenant_signature')->nullable();
            $table->timestamp('tenant_signed_at')->nullable();
            $table->longText('landlord_signature')->nullable();
            $table->timestamp('landlord_signed_at')->nullable();

            $table->string('lc1_name')->nullable();
            $table->string('lc1_phone')->nullable();
            $table->longText('lc1_signature')->nullable();
            $table->string('lc1_stamp')->nullable();
            $table->timestamp('lc1_signed_at')->nullable();

            $table->string('witness1_name')->nullable();
            $table->longText('witness1_signature')->nullable();
            $table->timestamp('witness1_signed_at')->nullable();

            $table->string('witness2_name')->nullable();
            $table->longText('witness2_signature')->nullable();
            $table->timestamp('witness2_signed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('property_units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenancy_contracts');
    }
};
