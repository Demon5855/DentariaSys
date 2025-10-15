<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();

            // Clave foránea para relacionar con la tabla pacientes
            $table->foreignId('paciente_id')->unique()->constrained('pacientes')->onDelete('cascade');

            $table->date('fecha_apertura');
            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();
            $table->text('examen_clinico_general')->nullable();
            // Puedes añadir más campos generales aquí si lo necesitas

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_clinicas');
    }
};
