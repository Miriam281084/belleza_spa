<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Empleados
        Schema::table('empleados', function (Blueprint $table) {
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        });

        // Servicios
        Schema::table('servicios', function (Blueprint $table) {
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        });

        // Productos
        Schema::table('productos', function (Blueprint $table) {
            $table->enum('estado', ['Activo', 'Inactivo'])->default('Activo');
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            $table->dropColumn('estado');
        });

        Schema::table('servicios', function (Blueprint $table) {
            $table->dropColumn('estado');
        });

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};