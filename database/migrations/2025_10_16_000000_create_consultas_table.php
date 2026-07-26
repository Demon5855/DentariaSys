<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Cada fila es una visita. Mapea a los bloques B-G del formulario
     * SNS-MSP/HCU-form.033/2021:
     *   B  motivo_consulta
     *   C  enfermedad_actual
     *   D  antecedentes_personales   (catálogo estructurado: fase 4)
     *   E  antecedentes_familiares   (catálogo estructurado: fase 4)
     *   F  temperatura / pulso / frecuencia_respiratoria / presion_arterial
     *   G  examen_estomatognatico
     *
     * En esta fase los antecedentes y el examen estomatognático quedan como
     * texto libre; el catálogo estructurado (11 regiones anatómicas, 10
     * antecedentes personales + 10 familiares) se modela en la fase 4 cuando
     * se construye el formulario completo.
     */
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')
                ->constrained('historia_clinicas')
                ->cascadeOnDelete();
            $table->foreignId('profesional_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('fecha');
            $table->text('motivo_consulta');
            $table->text('enfermedad_actual')->nullable();
            $table->text('antecedentes_personales')->nullable();
            $table->text('antecedentes_familiares')->nullable();

            // F. Constantes vitales
            $table->string('presion_arterial', 20)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->unsignedSmallInteger('pulso')->nullable();
            $table->unsignedSmallInteger('frecuencia_respiratoria')->nullable();

            // G. Examen del sistema estomatognático
            $table->text('examen_estomatognatico')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
