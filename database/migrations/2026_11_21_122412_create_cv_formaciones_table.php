<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CV: formación académica, cursos y certificaciones del alumno.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cv_formaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained()->cascadeOnDelete();
            $table->string('institucion', 150);
            $table->string('titulo', 150);
            $table->string('nivel', 20);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->boolean('en_curso')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_formaciones');
    }
};
