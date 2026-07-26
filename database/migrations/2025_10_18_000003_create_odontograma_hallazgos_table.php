<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('odontograma_hallazgos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontograma_pieza_id')->constrained('odontograma_piezas')->cascadeOnDelete();
            $table->foreignId('condicion_id')->constrained('condiciones')->restrictOnDelete();

            // NULL cuando la condición es de nivel 'pieza' (ej. corona,
            // endodoncia). Con valor cuando es de nivel 'superficie'
            // (caries, obturado): vestibular, palatina, lingual, mesial,
            // distal, oclusal, incisal.
            $table->string('superficie', 12)->nullable();

            $table->timestamps();

            $table->unique(['odontograma_pieza_id', 'condicion_id', 'superficie'], 'hallazgo_unico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontograma_hallazgos');
    }
};
