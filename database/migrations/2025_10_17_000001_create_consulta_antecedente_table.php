<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consulta_antecedente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas')->cascadeOnDelete();
            $table->foreignId('antecedente_id')->constrained('antecedentes')->cascadeOnDelete();
            $table->enum('tipo', ['personal', 'familiar']);
            $table->timestamps();

            $table->unique(['consulta_id', 'antecedente_id', 'tipo'], 'consulta_antecedente_unico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consulta_antecedente');
    }
};
