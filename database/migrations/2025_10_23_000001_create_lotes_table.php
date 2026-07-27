<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * `cantidad_actual` es una columna cacheada (no se calcula sumando
     * movimientos en cada consulta) — se actualiza atómicamente dentro de
     * una transacción cada vez que hay un movimiento. Mismo patrón que los
     * índices congelados del odontograma: más rápido de leer, y el
     * historial de movimientos sigue disponible para auditar cómo se
     * llegó a ese número.
     */
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();

            $table->string('numero_lote')->nullable(); // código del fabricante, si trae
            $table->date('fecha_caducidad');
            $table->date('fecha_ingreso');
            $table->string('proveedor')->nullable();
            $table->decimal('costo_unitario', 10, 2)->nullable();

            $table->unsignedInteger('cantidad_inicial');
            $table->unsignedInteger('cantidad_actual');

            $table->timestamps();

            $table->index(['producto_id', 'fecha_caducidad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
