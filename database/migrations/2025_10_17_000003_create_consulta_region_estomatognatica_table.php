<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consulta_region_estomatognatica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas')->cascadeOnDelete();
            $table->foreignId('region_estomatognatica_id')
                ->constrained('regiones_estomatognaticas')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['consulta_id', 'region_estomatognatica_id'], 'consulta_region_unica');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_region_estomatognatica');
    }
};
