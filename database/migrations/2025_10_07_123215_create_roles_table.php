<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La tabla de roles ahora la maneja Spatie Permission
        // Esta migración se mantiene para compatibilidad pero no crea nada
        // ya que Spatie crea su propia tabla 'roles'
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
