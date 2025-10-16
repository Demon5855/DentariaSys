<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id('pac_id');
            $table->string('pac_primer_nombre');
            $table->string('pac_segundo_nombre')->nullable();
            $table->string('pac_primer_apellido');
            $table->string('pac_segundo_apellido')->nullable();
            $table->date('pac_fecha_nacimiento');
            $table->string('pac_telefono', 15)->nullable();
            $table->string('pac_direccion')->nullable();
            $table->string('pac_email')->unique()->nullable();
            $table->boolean('pac_activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
