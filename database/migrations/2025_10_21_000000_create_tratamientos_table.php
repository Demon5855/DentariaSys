<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Sección O del Form 033: "Registrar la fecha correspondiente a la
     * sesión de tratamiento clínico, a continuación, registrar el
     * diagnóstico y/o la complicación, seguido el procedimiento a seguir,
     * las prescripciones y por último el código y firma."
     *
     * CORREGIDO tras revisar el instructivo oficial: sí lleva 'fecha'
     * propia. El formulario impreso tiene una celda de fecha por cada FILA
     * de tratamiento — el supuesto anterior ("una consulta = una fecha =
     * una sesión de tratamiento") no se sostiene contra la fuente
     * primaria. Sigue pendiente confirmar con el consultorio si en la
     * práctica real registran más de una sesión de tratamiento bajo la
     * misma consulta con fechas distintas (ver pendientes-dentariasys.md,
     * ítem G.17), pero el campo ya no puede faltar en el esquema.
     *
     * "Una vez terminado el tratamiento se escribirá ALTA": por eso el
     * campo 'estado' en vez de forzar siempre una 'próxima cita'.
     */
    public function up(): void
    {
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas')->cascadeOnDelete();
            $table->foreignId('profesional_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('fecha');
            $table->text('diagnostico_complicaciones')->nullable();
            $table->text('procedimiento');
            $table->text('prescripciones')->nullable();
            $table->date('proxima_cita')->nullable();
            $table->enum('estado', ['en_tratamiento', 'alta'])->default('en_tratamiento');
            $table->unsignedTinyInteger('orden')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
