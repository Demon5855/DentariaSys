<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();

            // Salida ligada a un tratamiento = descuento automático.
            // Nullable porque las entradas, mermas y ajustes manuales no
            // tienen tratamiento asociado.
            $table->foreignId('tratamiento_id')->nullable()->constrained('tratamientos')->nullOnDelete();

            $table->enum('tipo', ['entrada', 'salida', 'merma', 'ajuste']);
            $table->integer('cantidad'); // siempre positivo; el signo lo da 'tipo', no el número
            $table->string('motivo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_stock');
    }
};
