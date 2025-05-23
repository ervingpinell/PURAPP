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
        Schema::create('reservas', function (Blueprint $table) {
            $table->bigIncrements('id_reserva');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_tour');
            $table->decimal('precio_adulto', 10, 2);
            $table->decimal('precio_nino', 10, 2);
            $table->date('fecha_reserva');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->string('estado_reserva', 20)->default('Pendiente');
            $table->string('idioma_tour', 20)->default('Ingles');
            $table->text('notas')->nullable();
            $table->string('codigo_reserva', 15)->unique();
            $table->integer('cantidad_adultos');
            $table->integer('cantidad_ninos');
            $table->decimal('total_pago', 10, 2);
            $table->timestamps();


            // Relaciones
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('cascade');
            $table->foreign('id_tour')->references('id_tour')->on('tours')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
