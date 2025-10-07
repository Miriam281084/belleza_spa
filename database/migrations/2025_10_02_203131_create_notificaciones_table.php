<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
<<<<<<< HEAD
            $table->text('mensaje');
            $table->string('tipo', 50);
            $table->timestamp('fecha_envio')->useCurrent();
            $table->string('estado', 50)->default('pendiente');
=======
            $table->enum('tipo', ['recordatorio_turno', 'cumpleanios', 'promocion']);
            $table->text('mensaje');
            $table->timestamp('fecha_envio')->useCurrent();
            $table->enum('estado', ['enviado', 'pendiente', 'fallido'])->default('pendiente');
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
