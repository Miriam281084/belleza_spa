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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('turno_id')->nullable()->constrained('turnos')->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago', 50);
            $table->timestamp('fecha_pago')->useCurrent();
            $table->string('estado', 50)->default('pendiente');
=======
            $table->foreignId('turno_id')->nullable()->constrained('turnos')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->timestamp('fecha_pago')->useCurrent();
            $table->decimal('monto', 10, 2);
            $table->enum('metodo', ['efectivo', 'tarjeta', 'mercadopago']);
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
