<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * `numero_documento` cumple el rol que el instructivo del Form 033 le da
     * al "Número de Historia Clínica": es la cédula, o para extranjeros el
     * pasaporte / carné de refugiado, o un código temporal de 17 dígitos
     * emitido por estadística si el paciente no tiene ninguno. No existe un
     * campo separado de "número de historia" porque, según el instructivo,
     * literalmente son el mismo dato.
     */
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();

            $table->enum('tipo_documento', ['cedula', 'pasaporte', 'carnet_refugiado', 'temporal']);
            $table->string('numero_documento', 20)->unique();

            $table->string('primer_nombre');
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido');
            $table->string('segundo_apellido')->nullable();

            $table->enum('sexo', ['H', 'M']);
            $table->date('fecha_nacimiento');

            $table->string('telefono', 15)->nullable();
            $table->string('direccion')->nullable();
            $table->string('email')->unique()->nullable();

            // Representante legal: obligatorio en el formulario solo cuando
            // el paciente es menor de edad (ver StorePacienteRequest). Su
            // tipo de documento es un catálogo más corto que el del
            // paciente: sin 'temporal', porque ese código de 17 dígitos es
            // el sustituto del Número de Historia Clínica del PACIENTE
            // (lo emite estadística para identificar SU expediente) — un
            // representante no tiene expediente propio, así que no aplica;
            // se le exige un documento real (cédula, pasaporte o carné).
            $table->enum('representante_tipo_documento', ['cedula', 'pasaporte', 'carnet_refugiado'])->nullable();
            $table->string('representante_nombre')->nullable();
            $table->string('representante_documento', 20)->nullable();
            $table->string('representante_parentesco', 50)->nullable();
            $table->string('representante_telefono', 15)->nullable();

            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
