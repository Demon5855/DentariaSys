<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Sección M del Form 033: "Registrar el diagnóstico y el código CIE...
     * y en las columnas PRE y DEF, se marcará X que corresponde a
     * diagnóstico presuntivo o definitivo respectivamente, el orden de
     * registro dependerá de la complejidad y urgencia de tratamiento... de
     * acuerdo al criterio del profesional." Por eso: varios diagnósticos
     * por consulta (1:N), cada uno con su propio estado y un orden
     * explícito que refleja el criterio clínico, no solo el ID.
     *
     * Sin ruta de edición, igual que el odontograma: para pasar un
     * diagnóstico de presuntivo a definitivo se registra uno nuevo en una
     * consulta posterior, no se sobrescribe el anterior.
     */
    public function up(): void
    {
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas')->cascadeOnDelete();
            $table->foreignId('diagnostico_cie10_id')->constrained('diagnosticos_cie10')->restrictOnDelete();

            $table->text('descripcion'); // el diagnóstico en palabras, además del código
            $table->enum('estado', ['presuntivo', 'definitivo']);
            $table->unsignedTinyInteger('orden')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
