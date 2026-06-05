<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE orders MODIFY amount DECIMAL(15,2) NULL');
        DB::statement('ALTER TABLE orders MODIFY tax_amount DECIMAL(15,2) NULL');
        DB::statement('ALTER TABLE orders MODIFY subtotal DECIMAL(15,2) NULL');
        DB::statement('ALTER TABLE orders MODIFY total DECIMAL(15,2) NULL');
        DB::statement('ALTER TABLE orders MODIFY transaction_amount DECIMAL(15,2) NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE orders MODIFY amount DOUBLE(8,2) NULL');
        DB::statement('ALTER TABLE orders MODIFY tax_amount DOUBLE(8,2) NULL');
        DB::statement('ALTER TABLE orders MODIFY subtotal DOUBLE(8,2) NULL');
        DB::statement('ALTER TABLE orders MODIFY total DOUBLE(8,2) NULL');
        DB::statement('ALTER TABLE orders MODIFY transaction_amount DOUBLE(8,2) NULL');
    }
};
