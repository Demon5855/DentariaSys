<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * La agenda es una entidad administrativa, deliberadamente desacoplada
     * de historia_clinica/consulta: se puede agendar una cita para un
     * paciente que todavía no tiene historia clínica abierta. Cuando la
     * cita se atiende, el personal registra la consulta por el flujo
     * normal — este sistema no la genera automáticamente, para no forzar
     * un acoplamiento que en la práctica no siempre aplica (una cita
     * puede no derivar en un procedimiento clínico, por ejemplo si el
     * paciente solo viene por una consulta administrativa).
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('profesional_id')->nullable()->constrained('users')->nullOnDelete();

            $table->dateTime('fecha_hora');
            $table->unsignedSmallInteger('duracion_minutos')->default(30);
            $table->enum('estado', ['pendiente', 'confirmada', 'atendida', 'cancelada', 'no_asistio'])
                ->default('pendiente');

            $table->string('motivo')->nullable();
            $table->text('notas')->nullable(); // motivo de cancelación, observaciones internas, etc.

            $table->timestamps();

            $table->index(['profesional_id', 'fecha_hora']);
            $table->index('fecha_hora');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
