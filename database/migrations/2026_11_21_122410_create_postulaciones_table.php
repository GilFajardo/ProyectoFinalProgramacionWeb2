<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Postulaciones de alumnos a vacantes.
 * Regla de negocio: un alumno solo puede postularse una vez a cada vacante.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacante_id')->constrained()->cascadeOnDelete();
            $table->foreignId('alumno_id')->constrained()->cascadeOnDelete();
            $table->string('estatus', 20)->default('en_proceso')->index();
            $table->text('carta_presentacion')->nullable();
            $table->text('comentario_reclutador')->nullable();
            $table->timestamp('estatus_actualizado_en')->nullable();
            $table->timestamps();

            $table->unique(['vacante_id', 'alumno_id'], 'postulaciones_vacante_alumno_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};
