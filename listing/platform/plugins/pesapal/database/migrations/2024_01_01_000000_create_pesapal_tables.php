<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // No additional tables needed for PesaPal
        // Payment data is stored in the existing payments table
    }

    public function down(): void
    {
        // No tables to drop
    }
};

