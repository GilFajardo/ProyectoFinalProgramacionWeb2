<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Empresas que publican vacantes en la bolsa de trabajo.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->index();
            $table->string('razon_social', 200)->nullable();
            $table->string('rfc', 13)->nullable()->unique();
            $table->string('sector', 100)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('sitio_web')->nullable();
            $table->string('email_contacto', 150)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('municipio', 100)->default('San Luis Potosí');
            $table->string('estado', 100)->default('San Luis Potosí');
            $table->string('logo_path')->nullable();
            $table->boolean('verificada')->default(false);
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
