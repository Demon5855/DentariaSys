<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Catálogo de la sección G (examen del sistema estomatognático).
     *
     * ⚠ El instructivo describe la metodología de examen para más regiones
     * (incluye "cavidad oral" y "características de la saliva" como puntos
     * aparte) de las que asumí como casillas independientes en la réplica
     * del formulario. Mantengo aquí las 12 que usamos en esa réplica;
     * confírmalo contra el formulario impreso antes de dar esto por
     * definitivo — es el mismo tipo de detalle que ya señalé como
     * pendiente de verificación en la fase del formulario.
     */
    public function up(): void
    {
        Schema::create('regiones_estomatognaticas', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('numero'); // 1-12, tal como en el formulario impreso
            $table->string('nombre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regiones_estomatognaticas');
    }
};
