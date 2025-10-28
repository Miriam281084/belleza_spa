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
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('mercadopago_preference_id')->nullable()->after('estado');
            $table->string('mercadopago_payment_id')->nullable()->after('mercadopago_preference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['mercadopago_preference_id', 'mercadopago_payment_id']);
        });
    }
};
