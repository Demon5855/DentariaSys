<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Una fila por cada pieza que tiene algún hallazgo en este odontograma
     * (no las 32 siempre — solo las que el odontólogo marcó). Notación FDI:
     * 11-48 definitivas, 51-85 temporales.
     */
    public function up(): void
    {
        Schema::create('odontograma_piezas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontograma_id')->constrained('odontogramas')->cascadeOnDelete();
            $table->unsignedTinyInteger('pieza'); // FDI: 11-48 o 51-85

            // Índice modificado de Miller. Solo aplica a dientes definitivos
            // (instructivo: "Registrar el índice de movilidad y recesión
            // solo en dientes definitivos").
            $table->unsignedTinyInteger('movilidad')->nullable(); // 0-3
            $table->unsignedTinyInteger('recesion')->nullable(); // 0-4

            $table->timestamps();

            $table->unique(['odontograma_id', 'pieza']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontograma_piezas');
    }
};
