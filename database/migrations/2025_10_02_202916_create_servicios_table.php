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
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('duracion')->comment('duracion en minutos');
=======
            $table->string('nombre_servicio', 150);
            $table->text('descripcion')->nullable();
            $table->integer('duracion')->comment('Duraci\u00f3n en minutos');
            $table->decimal('precio', 10, 2);
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
