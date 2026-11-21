<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ofertas de empleo. Cada vacante pertenece a una sola empresa
 * y tiene un periodo de vigencia (fecha_inicio a fecha_fin).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vacantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reclutador_id')->nullable()->constrained('reclutadores')->nullOnDelete();
            $table->foreignId('categoria_id')->constrained()->restrictOnDelete();
            $table->string('titulo', 150);
            $table->string('slug', 180)->unique();
            $table->text('descripcion');
            $table->text('requisitos')->nullable();
            $table->text('prestaciones')->nullable();
            $table->string('modalidad', 20)->default('presencial');
            $table->string('tipo_contrato', 30);
            $table->decimal('salario_min', 10, 2)->nullable();
            $table->decimal('salario_max', 10, 2)->nullable();
            $table->string('ubicacion', 150)->nullable();
            $table->unsignedSmallInteger('plazas')->default(1);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['activa', 'fecha_inicio', 'fecha_fin'], 'vacantes_vigencia_index');
            $table->index(['categoria_id', 'activa'], 'vacantes_categoria_activa_index');
        });

        // Índice de texto completo para la búsqueda en sitio (solo MySQL/MariaDB).
        if (in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            Schema::table('vacantes', function (Blueprint $table) {
                $table->fullText(['titulo', 'descripcion'], 'vacantes_busqueda_fulltext');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacantes');
    }
};
