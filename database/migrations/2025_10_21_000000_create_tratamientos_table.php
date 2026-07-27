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
     * No tiene columna 'fecha' propia: la fecha de la sesión es la de la
     * consulta a la que pertenece — cada visita nueva ya es una consulta
     * nueva en este sistema, así que una fecha de tratamiento aparte sería
     * un dato redundante que podría desincronizarse.
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
