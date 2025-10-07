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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
<<<<<<< HEAD
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->string('categoria', 100)->nullable();
=======
            $table->string('nombre_producto', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
>>>>>>> f24e2ba2bb9ad6aefdb86e2dad1670bca014d857
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
