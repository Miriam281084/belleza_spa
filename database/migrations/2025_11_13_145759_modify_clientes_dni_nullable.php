<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Primero, eliminar el índice unique existente
            $table->dropUnique(['dni']);

            // Hacer el campo nullable
            $table->string('dni', 20)->nullable()->change();
        });

        // Ahora que el campo es nullable, actualizar los DNI vacíos a NULL
        DB::table('clientes')->where('dni', '')->update(['dni' => null]);

        Schema::table('clientes', function (Blueprint $table) {
            // Volver a crear el índice unique (permite múltiples NULL)
            $table->unique('dni');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Eliminar el índice unique
            $table->dropUnique(['dni']);

            // Volver a hacer el campo NOT NULL
            $table->string('dni', 20)->nullable(false)->change();

            // Recrear el índice unique
            $table->unique('dni');
        });
    }
};
