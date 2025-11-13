<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Correo nuevo en espera de confirmación
            $table->string('pending_email')->nullable()->after('email');

            // Token único para confirmar el cambio
            $table->string('pending_email_token', 64)->nullable()->after('pending_email');

            // Cuándo se solicitó el cambio
            $table->timestamp('pending_email_created_at')->nullable()->after('pending_email_token');

            $table->index('pending_email');
            $table->index('pending_email_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['pending_email']);
            $table->dropIndex(['pending_email_token']);

            $table->dropColumn([
                'pending_email',
                'pending_email_token',
                'pending_email_created_at',
            ]);
        });
    }
};
