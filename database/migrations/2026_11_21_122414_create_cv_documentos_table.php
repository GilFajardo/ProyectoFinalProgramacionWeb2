<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CV: documentos probatorios (certificados, constancias, cartas) del alumno.
 * Los archivos se guardan en el disco privado "local".
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cv_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained()->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('tipo', 30);
            $table->string('archivo_path');
            $table->string('mime', 100)->nullable();
            $table->unsignedInteger('tamano_kb')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_documentos');
    }
};
