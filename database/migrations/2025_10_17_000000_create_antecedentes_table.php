<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Catálogo de las secciones D y E del Form 033 (antecedentes patológicos
     * personales y familiares). Es UN solo catálogo porque el instructivo
     * usa la misma lista de 10 ítems en ambas secciones — lo que cambia es
     * si se marca como "personal" o "familiar" al vincularlo con la
     * consulta (ver consulta_antecedente).
     */
    public function up(): void
    {
        Schema::create('antecedentes', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('codigo'); // 1-10, tal como en el formulario impreso
            $table->string('nombre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antecedentes');
    }
};
