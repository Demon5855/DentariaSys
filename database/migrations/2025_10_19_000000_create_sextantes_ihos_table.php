<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * El instructivo define, por sextante, una pieza primaria y una
     * alterna en dentición permanente, más una pieza equivalente en
     * dentición temporal para pacientes en dentición mixta o temporal.
     * Regla de sustitución: si la primaria no está en boca, se examina
     * la alterna; si ninguna está presente, el sextante se marca "—" (no
     * se evalúa, no cuenta en el promedio).
     */
    public function up(): void
    {
        Schema::create('sextantes_ihos', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('numero'); // 1-6
            $table->unsignedTinyInteger('pieza_primaria'); // FDI, permanente
            $table->unsignedTinyInteger('pieza_alterna'); // FDI, permanente
            $table->unsignedTinyInteger('pieza_temporal')->nullable(); // FDI, temporal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sextantes_ihos');
    }
};
