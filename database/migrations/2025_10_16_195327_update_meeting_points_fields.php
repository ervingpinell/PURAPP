<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aumenta longitud del campo name, reemplaza 'address' por 'description',
     * y copia los datos sin requerir doctrine/dbal.
     */
    public function up(): void
    {
        // 1️⃣ Crear nueva columna description
        Schema::table('meeting_points', function (Blueprint $table) {
            $table->string('description', 1000)
                ->nullable()
                ->after('pickup_time');
        });

        // 2️⃣ Copiar datos existentes de address → description usando SQL directo
        DB::statement('UPDATE meeting_points SET description = address');

        // 3️⃣ Eliminar la columna antigua
        Schema::table('meeting_points', function (Blueprint $table) {
            $table->dropColumn('address');
        });

        // 4️⃣ Ampliar longitud del campo name (sin usar change())
        DB::statement('ALTER TABLE meeting_points ALTER COLUMN name TYPE VARCHAR(1000)');
    }

    /**
     * Revierte los cambios al estado original.
     */
    public function down(): void
    {
        // 1️⃣ Crear nuevamente la columna address
        Schema::table('meeting_points', function (Blueprint $table) {
            $table->string('address', 255)
                ->nullable()
                ->after('pickup_time');
        });

        // 2️⃣ Copiar datos de description → address usando SQL directo
        DB::statement('UPDATE meeting_points SET address = description');

        // 3️⃣ Eliminar la columna description
        Schema::table('meeting_points', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        // 4️⃣ Devolver la longitud original del campo name
        DB::statement('ALTER TABLE meeting_points ALTER COLUMN name TYPE VARCHAR(255)');
    }
};
