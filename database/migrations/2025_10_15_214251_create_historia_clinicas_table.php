<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * `historia_clinicas` es la carpeta del paciente: se abre una sola vez.
     * Los datos que cambian en cada visita (motivo de consulta, antecedentes,
     * examen del sistema estomatognático) viven en `consultas`, no aquí.
     */
    public function up(): void
    {
        Schema::create('historia_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')
                ->unique()
                ->constrained('pacientes')
                ->cascadeOnDelete();
            $table->date('fecha_apertura');
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
