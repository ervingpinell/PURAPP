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
        // USERS
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id('user_id');
                $table->string('full_name', 100);
                $table->string('email', 200)->unique();
                $table->string('password', 255);
                $table->boolean('status')->default(true);
                $table->unsignedBigInteger('role_id');
                $table->string('phone', 20)->nullable();
                $table->boolean('is_active')->default(true);

                $table->timestamps();

                // Asegúrate de que la tabla roles exista en una migración previa
                $table->foreign('role_id')
                    ->references('role_id')->on('roles')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete(); // ajusta a cascadeOnDelete() si así lo necesitas
            });
        }

        // PASSWORD RESET TOKENS
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // SESSIONS
        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                // si usas PK 'user_id' en users, foreignId()->constrained('users','user_id') funcionará
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                // en PG puedes usar text; en MySQL largo también es seguro
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropear en orden inverso para evitar conflictos de FK
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');

        // users al final por posibles FKs hacia ella
        Schema::dropIfExists('users');
    }
};
