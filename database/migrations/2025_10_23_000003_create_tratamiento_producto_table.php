<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Al guardar un tratamiento con productos aquí, el sistema descuenta
     * el stock automáticamente (FIFO/FEFO: primero el lote que vence
     * antes) y genera los movimientos_stock de tipo 'salida'
     * correspondientes — ver Producto::descontarStock().
     */
    public function up(): void
    {
        Schema::create('tratamiento_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tratamiento_id')->constrained('tratamientos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->unsignedInteger('cantidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamiento_producto');
    }
};
