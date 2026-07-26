<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * El instructivo es explícito: "una vez registrado el odontograma no
     * podrá ser alterado (repintados, tachado, aumentado)". Por eso este
     * modelo no tiene ruta de edición — solo create/show. Una corrección
     * se hace registrando un odontograma nuevo de tipo 'evolutivo'.
     *
     * Se cuelga de historia_clinica (el ciclo de vigencia), no de consulta:
     * el odontograma inicial se registra al abrir, y los evolutivos pueden
     * surgir en cualquier consulta posterior dentro del mismo ciclo — por
     * eso consulta_id es nullable, solo para trazabilidad de en qué visita
     * se registró.
     *
     * Los índices se CONGELAN aquí al firmar. Si mañana cambia la fórmula
     * de cálculo, los odontogramas viejos no deben cambiar de valor con
     * efecto retroactivo.
     */
    public function up(): void
    {
        Schema::create('odontogramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historia_clinicas')->cascadeOnDelete();
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->nullOnDelete();
            $table->foreignId('odontologo_id')->constrained('users')->restrictOnDelete();

            $table->enum('tipo', ['inicial', 'evolutivo']);
            $table->enum('denticion', ['permanente', 'temporal', 'mixta']);
            $table->date('fecha');
            $table->timestamp('firmado_at');

            // Índices congelados. ceod usa 'e' en vez de 'p' (extraída) por
            // convención del CPO-D en dentición temporal.
            $table->unsignedTinyInteger('cpod_c')->default(0);
            $table->unsignedTinyInteger('cpod_p')->default(0);
            $table->unsignedTinyInteger('cpod_o')->default(0);
            $table->unsignedTinyInteger('ceod_c')->default(0);
            $table->unsignedTinyInteger('ceod_e')->default(0);
            $table->unsignedTinyInteger('ceod_o')->default(0);

            $table->timestamps();

            $table->index(['historia_clinica_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontogramas');
    }
};
