<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->bigIncrements('id_tour');
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio_adulto', 10, 2);
            $table->decimal('precio_nino', 10, 2);
            $table->integer('duracion_horas');
            $table->string('ubicacion');
            $table->enum('tipo_tour', ['Half Day', 'Full Day']);
            $table->string('idioma_disponible')->default('Inglés');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
