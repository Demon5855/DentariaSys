<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Catálogo, no enum en código — el MSP ya cambió esta simbología entre
     * la versión 2008 y la 2021 del formulario. Con tabla, agregar una
     * condición nueva no exige migración.
     */
    public function up(): void
    {
        Schema::create('condiciones', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique(); // 'caries', 'endodoncia_ind', etc — usado en JS/PHP
            $table->string('nombre');
            $table->enum('nivel', ['superficie', 'pieza']);
            $table->enum('color', ['rojo', 'azul']);
            $table->string('simbolo', 10)->nullable(); // null en condiciones de superficie (se pintan, no se marcan con símbolo)

            // Cómo cuenta esta condición en el índice CPO-D / ceo-d. Ver
            // instructivo: endodoncia por realizar cuenta como Cariada,
            // realizada como Obturada — el símbolo es el mismo, el índice no.
            $table->enum('afecta_indice', ['C', 'P', 'O'])->nullable();

            $table->boolean('solo_definitivas')->default(false); // movilidad/recesión/sellantes
            $table->boolean('excluye_terceros_molares')->default(false); // prótesis total

            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('condiciones');
    }
};
