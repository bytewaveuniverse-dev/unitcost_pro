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
        Schema::create('configuracions', function (Blueprint $table) {
            $table->id();
            // Nombre del parámetro (ej: 'iva', 'utilidad', 'bono_alimentacion')
            $table->string('descripcion'); 
            // El valor (lo ponemos string por si necesitas guardar algo que no sea solo números)
            $table->string('valor'); 
            // Relación con el usuario (Analista o Administrador)
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); 
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracions');
    }
};
