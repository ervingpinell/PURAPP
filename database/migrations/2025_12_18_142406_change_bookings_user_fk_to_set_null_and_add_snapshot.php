<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add snapshot fields to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('user_email', 255)->nullable()->after('user_id');
            $table->string('user_full_name', 255)->nullable()->after('user_email');
            $table->string('user_phone', 50)->nullable()->after('user_full_name');
            $table->boolean('user_was_guest')->default(false)->after('user_phone');
        });

        // Step 2: Populate snapshot fields for existing bookings using Eloquent
        \App\Models\Booking::with('user')->chunk(100, function ($bookings) {
            foreach ($bookings as $booking) {
                if ($booking->user) {
                    $booking->update([
                        'user_email' => $booking->user->email,
                        'user_full_name' => $booking->user->full_name,
                        'user_phone' => $booking->user->phone,
                        'user_was_guest' => (bool) ($booking->user->is_guest ?? false),
                    ]);
                }
            }
        });

        // Step 3: Drop existing foreign key
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Step 4: Drop ALL dependent views temporarily
        // Note: These views will need to be recreated manually or via separate migration
        DB::statement("DROP VIEW IF EXISTS v_booking_category_facts CASCADE");
        DB::statement("DROP VIEW IF EXISTS v_booking_facts CASCADE");
        DB::statement("DROP VIEW IF EXISTS v_bookings CASCADE");

        // Step 5: Make user_id nullable
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });

        // Step 6: Recreate foreign key with SET NULL
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Drop the new foreign key
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        // Step 2: Make user_id NOT nullable again (only if all have values)
        // WARNING: This will fail if there are any NULL user_id values
        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });

        // Step 3: Recreate original foreign key with CASCADE
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        // Step 4: Drop snapshot fields
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'user_email',
                'user_full_name',
                'user_phone',
                'user_was_guest',
            ]);
        });
    }
};
