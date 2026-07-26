<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * `historia_clinicas` es la carpeta del paciente. IMPORTANTE: un paciente
     * puede tener VARIAS a lo largo del tiempo, no una sola — el instructivo
     * del Form 033 es explícito: el diagnóstico dura un año calendario, y
     * "cuando el paciente regresa a la consulta después de un año calendario,
     * se volverá a abrir un nuevo formulario 033". Esa misma vigencia cambia
     * en dos casos: embarazadas (dura el período de gestación) y escolares
     * (dura el año lectivo). Por eso NO hay unique() en paciente_id, y por
     * eso existen tipo_vigencia + fecha_vencimiento.
     */
    public function up(): void
    {
        Schema::create('historia_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')
                ->constrained('pacientes')
                ->cascadeOnDelete();

            $table->date('fecha_apertura');

            $table->enum('tipo_vigencia', ['general', 'embarazo', 'escolar'])
                ->default('general');

            // Fecha en que este 033 deja de estar vigente y hay que abrir
            // uno nuevo. Se calcula al crear, según tipo_vigencia:
            //   general  -> fecha_apertura + 365 días
            //   embarazo -> fecha_probable_parto
            //   escolar  -> fecha_fin_periodo_lectivo
            $table->date('fecha_vencimiento');

            // Solo se usan según tipo_vigencia; se guardan para poder
            // mostrar/editar el dato de origen, no solo el resultado.
            $table->date('fecha_probable_parto')->nullable();
            $table->date('fecha_fin_periodo_lectivo')->nullable();

            $table->timestamps();

            $table->index(['paciente_id', 'fecha_apertura']);
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
