<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Tipo de atributo: color, tono o ninguno
            $table->enum('atributo_tipo', ['color', 'tono', 'ninguno'])->default('ninguno');

            // Datos para el atributo tipo 'color'
            $table->string('color')->nullable();              // Código hexadecimal
            $table->string('nombre_color')->nullable();       // Nombre legible

            // Datos para el atributo tipo 'tono'
            $table->string('tono')->nullable();               // Código hexadecimal
            $table->string('nombre_tono')->nullable();        // Nombre legible

            $table->integer('stock')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
