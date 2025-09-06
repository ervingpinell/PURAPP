<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->foreignId('meeting_point_id')
                  ->nullable()
                  ->constrained('meeting_points')
                  ->nullOnDelete()
                  ->after('hotel_id');

            $table->string('meeting_point_name')->nullable()->after('meeting_point_id');
            $table->string('meeting_point_pickup_time', 20)->nullable()->after('meeting_point_name');
            $table->string('meeting_point_address')->nullable()->after('meeting_point_pickup_time');
            $table->string('meeting_point_map_url')->nullable()->after('meeting_point_address');
        });
    }

    public function down(): void
    {
        Schema::table('booking_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('meeting_point_id');
            $table->dropColumn([
                'meeting_point_name',
                'meeting_point_pickup_time',
                'meeting_point_address',
                'meeting_point_map_url',
            ]);
        });
    }
};
