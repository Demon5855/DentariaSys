<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('odontograma_ihos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontograma_id')->constrained('odontogramas')->cascadeOnDelete();
            $table->foreignId('sextante_ihos_id')->constrained('sextantes_ihos')->restrictOnDelete();

            // NULL = el sextante se marcó "—" (ninguna pieza candidata
            // presente); no participa del promedio.
            $table->unsignedTinyInteger('pieza_examinada')->nullable();
            $table->unsignedTinyInteger('placa')->nullable(); // 0-3
            $table->unsignedTinyInteger('calculo')->nullable(); // 0-3
            $table->unsignedTinyInteger('gingivitis')->nullable(); // 0-1

            $table->timestamps();

            $table->unique(['odontograma_id', 'sextante_ihos_id'], 'odontograma_ihos_unico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontograma_ihos');
    }
};
