<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE empleados
            MODIFY COLUMN rol ENUM(
                'admin',
                'recepcionista',
                'esteticista',
                'masajista',
                'manicurista'
            ) NOT NULL DEFAULT 'esteticista'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE empleados
            MODIFY COLUMN rol ENUM(
                'admin',
                'recepcionista',
                'esteticista'
            ) NOT NULL DEFAULT 'esteticista'
        ");
    }
};
