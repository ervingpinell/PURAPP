<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            // TIME (sin zona), nullable
            $table->time('pickup_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropColumn('pickup_time');
        });
    }
};
