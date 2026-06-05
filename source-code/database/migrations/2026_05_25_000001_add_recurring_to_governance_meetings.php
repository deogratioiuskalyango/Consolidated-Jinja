<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $this->addColumn('is_recurring', fn (Blueprint $table) => $table->boolean('is_recurring')->default(false)->after('notes'));
        $this->addColumn('recurrence_type', fn (Blueprint $table) => $table->string('recurrence_type')->nullable()->after('is_recurring'));
        $this->addColumn('recurrence_day_of_week', fn (Blueprint $table) => $table->tinyInteger('recurrence_day_of_week')->nullable()->after('recurrence_type'));
        $this->addColumn('recurrence_day_of_month', fn (Blueprint $table) => $table->tinyInteger('recurrence_day_of_month')->nullable()->after('recurrence_day_of_week'));
        $this->addColumn('recurrence_end_date', fn (Blueprint $table) => $table->date('recurrence_end_date')->nullable()->after('recurrence_day_of_month'));
        $this->addColumn('parent_meeting_id', fn (Blueprint $table) => $table->unsignedBigInteger('parent_meeting_id')->nullable()->index()->after('recurrence_end_date'));
    }

    public function down(): void
    {
        foreach ([
            'parent_meeting_id',
            'recurrence_end_date',
            'recurrence_day_of_month',
            'recurrence_day_of_week',
            'recurrence_type',
            'is_recurring',
        ] as $column) {
            if (Schema::hasColumn('governance_meetings', $column)) {
                Schema::table('governance_meetings', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    private function addColumn(string $column, callable $definition): void
    {
        if (!Schema::hasColumn('governance_meetings', $column)) {
            Schema::table('governance_meetings', function (Blueprint $table) use ($definition) {
                $definition($table);
            });
        }
    }
};
