<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * ⚠ Este catálogo es un punto de partida (bloque K00-K14, enfermedades
     * de la cavidad oral, glándulas salivales y maxilares), NO la tabla
     * CIE-10 oficial completa que usa el MSP. K00-K14 es un bloque estable
     * del estándar internacional, pero no pude verificar contra la
     * implementación exacta que usa el sistema de salud ecuatoriano.
     * Antes de producción, reemplazar/ampliar con la tabla oficial —ver
     * DiagnosticoCie10Seeder.
     */
    public function up(): void
    {
        Schema::create('diagnosticos_cie10', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10)->unique(); // ej. 'K02.9'
            $table->string('descripcion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnosticos_cie10');
    }
};
