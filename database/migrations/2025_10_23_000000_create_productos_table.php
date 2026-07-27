<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * El stock real vive en `lotes`, no aquí — un producto es el catálogo
     * (qué es, cómo se llama, cuál es su código de barras), un lote es
     * cada entrada física con su propia fecha de caducidad. Un producto
     * puede tener cero, uno o varios lotes activos a la vez.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo_barras')->unique()->nullable(); // nullable: no todo insumo trae código de fábrica
            $table->string('unidad_medida', 20)->default('unidad'); // unidad, caja, ml, g...
            $table->string('categoria')->nullable(); // texto libre, no catálogo — evita sobre-ingeniería
            $table->unsignedInteger('stock_minimo')->default(0); // umbral para alertar reposición
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
