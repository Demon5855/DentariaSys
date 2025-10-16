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
        Schema::create('historia_clinicas', function (Blueprint $table) {
            $table->id('hcl_id');
            $table->unsignedBigInteger('hcl_pac_id')->unique();
            $table->foreign('hcl_pac_id')->references('pac_id')->on('pacientes')->onDelete('cascade');
            $table->date('hcl_fecha_apertura');
            $table->text('hcl_antecedentes_personales')->nullable();
            $table->text('hcl_antecedentes_familiares')->nullable();
            $table->text('hcl_examen_clinico_general')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historia_clinicas');
    }
};
