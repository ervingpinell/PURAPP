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
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la foreign key constraint a la tabla old_roles
            $table->dropForeign(['role_id']);

            // Eliminar la columna role_id ya que usaremos Spatie exclusivamente
            $table->dropColumn('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurar la columna role_id
            $table->unsignedBigInteger('role_id')->nullable()->after('status');

            // Restaurar la foreign key (apuntando a old_roles para mantener consistencia)
            $table->foreign('role_id')->references('role_id')->on('old_roles')->onDelete('set null');
        });
    }
};
