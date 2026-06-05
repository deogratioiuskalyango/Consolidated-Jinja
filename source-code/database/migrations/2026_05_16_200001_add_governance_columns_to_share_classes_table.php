<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addColumn('class_code', fn (Blueprint $table) => $table->string('class_code', 5)->nullable()->after('type'));
        $this->addColumn('voting_multiplier', fn (Blueprint $table) => $table->decimal('voting_multiplier', 8, 4)->default(1.0)->after('voting_weight'));
        $this->addColumn('dividend_priority', fn (Blueprint $table) => $table->tinyInteger('dividend_priority')->default(10)->after('dividend_rights'));
        $this->addColumn('is_transferable', fn (Blueprint $table) => $table->boolean('is_transferable')->default(true)->after('dividend_priority'));
        $this->addColumn('transfer_requires_board_approval', fn (Blueprint $table) => $table->boolean('transfer_requires_board_approval')->default(false)->after('is_transferable'));
        $this->addColumn('transfer_requires_compliance_review', fn (Blueprint $table) => $table->boolean('transfer_requires_compliance_review')->default(false)->after('transfer_requires_board_approval'));
        $this->addColumn('can_be_diluted_without_approval', fn (Blueprint $table) => $table->boolean('can_be_diluted_without_approval')->default(true)->after('transfer_requires_compliance_review'));
        $this->addColumn('has_vesting', fn (Blueprint $table) => $table->boolean('has_vesting')->default(false)->after('can_be_diluted_without_approval'));
        $this->addColumn('is_founder_class', fn (Blueprint $table) => $table->boolean('is_founder_class')->default(false)->after('has_vesting'));
        $this->addColumn('governance_level', fn (Blueprint $table) => $table->tinyInteger('governance_level')->default(1)->after('is_founder_class'));
        $this->addColumn('financial_approval_limit', fn (Blueprint $table) => $table->decimal('financial_approval_limit', 20, 4)->nullable()->after('governance_level'));
        $this->addColumn('max_ownership_cap', fn (Blueprint $table) => $table->decimal('max_ownership_cap', 8, 4)->nullable()->after('financial_approval_limit'));
        $this->addColumn('fixed_dividend_rate', fn (Blueprint $table) => $table->decimal('fixed_dividend_rate', 8, 4)->nullable()->after('max_ownership_cap'));
        $this->addColumn('dividend_cumulative', fn (Blueprint $table) => $table->boolean('dividend_cumulative')->default(false)->after('fixed_dividend_rate'));
        $this->addColumn('allowed_resolution_types', fn (Blueprint $table) => $table->json('allowed_resolution_types')->nullable()->after('dividend_cumulative'));
        $this->addColumn('governance_notes', fn (Blueprint $table) => $table->text('governance_notes')->nullable()->after('allowed_resolution_types'));
    }

    public function down(): void
    {
        foreach ([
            'governance_notes',
            'allowed_resolution_types',
            'dividend_cumulative',
            'fixed_dividend_rate',
            'max_ownership_cap',
            'financial_approval_limit',
            'governance_level',
            'is_founder_class',
            'has_vesting',
            'can_be_diluted_without_approval',
            'transfer_requires_compliance_review',
            'transfer_requires_board_approval',
            'is_transferable',
            'dividend_priority',
            'voting_multiplier',
            'class_code',
        ] as $column) {
            if (Schema::hasColumn('share_classes', $column)) {
                Schema::table('share_classes', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    private function addColumn(string $column, callable $definition): void
    {
        if (!Schema::hasColumn('share_classes', $column)) {
            Schema::table('share_classes', function (Blueprint $table) use ($definition) {
                $definition($table);
            });
        }
    }
};
